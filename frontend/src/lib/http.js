import axios from 'axios'

export const TOKEN_STORAGE_KEY = 'covoiturage_tn_token'

export function getStoredToken() {
  try {
    return localStorage.getItem(TOKEN_STORAGE_KEY)
  } catch {
    return null
  }
}

export function setStoredToken(token) {
  try {
    if (token) {
      localStorage.setItem(TOKEN_STORAGE_KEY, token)
    } else {
      localStorage.removeItem(TOKEN_STORAGE_KEY)
    }
  } catch {
    // Ignore storage failures (private browsing, etc).
  }
}

export const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  headers: {
    Accept: 'application/json',
  },
})

http.interceptors.request.use((config) => {
  const token = getStoredToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

/**
 * Listeners the app (mainly the auth store) can register to react to
 * cross-cutting HTTP events without this module importing Pinia stores
 * directly (which would create a circular dependency).
 */
const handlers = {
  unauthorized: [],
  serverError: [],
  network: [],
}

export function onHttpEvent(event, callback) {
  handlers[event]?.push(callback)
}

http.interceptors.response.use(
  (response) => response,
  (error) => {
    if (!error.response) {
      handlers.network.forEach((cb) => cb(error))
    } else if (error.response.status === 401) {
      handlers.unauthorized.forEach((cb) => cb(error))
    } else if (error.response.status >= 500) {
      handlers.serverError.forEach((cb) => cb(error))
    }

    return Promise.reject(error)
  },
)

/**
 * Whether a form/component should show its own toast for this error, or
 * stay quiet because the global interceptor above already surfaced one
 * (401 -> session expired, 5xx -> generic server error, no response ->
 * network error).
 */
export function isGloballyHandled(error) {
  if (!error.response) return true
  return error.response.status === 401 || error.response.status >= 500
}

/**
 * Extract a single human-readable message from an Axios/Laravel error,
 * without ever surfacing raw stack traces or internal exception detail.
 */
export function extractErrorMessage(error, fallback = 'Something went wrong. Please try again.') {
  const data = error?.response?.data
  if (data?.message) {
    return data.message
  }
  if (data?.errors) {
    const first = Object.values(data.errors)[0]
    if (Array.isArray(first) && first.length) {
      return first[0]
    }
  }
  return fallback
}
