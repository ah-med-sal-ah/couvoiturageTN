import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

import * as authApi from '@/api/auth'
import * as userApi from '@/api/user'
import { applyLocale } from '@/i18n'
import { extractErrorMessage, getStoredToken, setStoredToken } from '@/lib/http'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(getStoredToken())
  const initializing = ref(true)
  const loginLoading = ref(false)
  const registerLoading = ref(false)
  const logoutLoading = ref(false)

  const isAuthenticated = computed(() => Boolean(token.value && user.value))

  function setSession(newUser, newToken) {
    user.value = newUser
    token.value = newToken
    setStoredToken(newToken)
    if (newUser?.language) {
      applyLocale(newUser.language)
    }
  }

  function clearSession() {
    user.value = null
    token.value = null
    setStoredToken(null)
  }

  async function login(credentials) {
    loginLoading.value = true
    try {
      const { data } = await authApi.login(credentials)
      setSession(data.user, data.token)
      return data.user
    } catch (error) {
      throw new Error(extractErrorMessage(error, 'Invalid username or password.'))
    } finally {
      loginLoading.value = false
    }
  }

  async function register(payload) {
    registerLoading.value = true
    try {
      const { data } = await authApi.register(payload)
      setSession(data.user, data.token)
      return data.user
    } catch (error) {
      const validationError = new Error(extractErrorMessage(error, 'Could not create your account.'))
      validationError.fields = error?.response?.data?.errors ?? null
      throw validationError
    } finally {
      registerLoading.value = false
    }
  }

  async function logout() {
    logoutLoading.value = true
    try {
      if (token.value) {
        await authApi.logout().catch(() => {
          // Even if the API call fails (e.g. token already expired),
          // still clear the local session below.
        })
      }
    } finally {
      clearSession()
      logoutLoading.value = false
    }
  }

  /**
   * Restore the session on app boot by validating the stored token.
   *
   * Memoized: the router guard also calls this on every navigation (see
   * router/index.js) so it never judges isAuthenticated before the token
   * has actually been checked, regardless of whether the router's first
   * navigation resolves before or after main.js's own call finishes. Both
   * callers share the same one in-flight/completed check.
   */
  let initPromise = null
  function initialize() {
    if (initPromise) return initPromise

    initPromise = (async () => {
      if (!token.value) {
        initializing.value = false
        return
      }

      try {
        const { data } = await authApi.fetchCurrentUser()
        user.value = data.data
        if (user.value?.language) {
          applyLocale(user.value.language)
        }
      } catch {
        clearSession()
      } finally {
        initializing.value = false
      }
    })()

    return initPromise
  }

  async function updateProfile(payload) {
    const { data } = await userApi.updateProfile(payload)
    user.value = data.data
    return user.value
  }

  async function updatePassword(payload) {
    await userApi.updatePassword(payload)
  }

  async function setLanguage(locale) {
    applyLocale(locale)
    if (isAuthenticated.value) {
      await userApi.updateProfile({ language: locale }).then(({ data }) => {
        user.value = data.data
      })
    }
  }

  return {
    user,
    token,
    initializing,
    loginLoading,
    registerLoading,
    logoutLoading,
    isAuthenticated,
    login,
    register,
    logout,
    initialize,
    updateProfile,
    updatePassword,
    setLanguage,
    clearSession,
  }
})
