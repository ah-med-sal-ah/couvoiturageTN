<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'

import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const form = reactive({ username: '', password: '' })
const errorMessage = ref('')

async function submit() {
  errorMessage.value = ''
  try {
    await auth.login(form)
    router.push(route.query.redirect || { name: 'home' })
  } catch (error) {
    errorMessage.value = error.message
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-10">
    <div class="w-full max-w-sm">
      <div class="mb-8 flex flex-col items-center text-center">
        <img src="/images/couvoiturage.png" alt="CovoiturageTN" class="mb-3 h-14 w-14 rounded-xl object-cover shadow-card" />
        <h1 class="text-xl font-semibold text-slate-900">{{ t('auth.login.title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ t('auth.login.subtitle') }}</p>
      </div>

      <form class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-card" @submit.prevent="submit">
        <p v-if="errorMessage" class="rounded-lg bg-passenger-50 px-3 py-2 text-sm font-medium text-passenger-700">
          {{ errorMessage }}
        </p>

        <div>
          <label for="login-username" class="form-label">{{ t('auth.login.username') }}</label>
          <input id="login-username" v-model="form.username" name="username" type="text" required autocomplete="username" class="form-input" />
        </div>
        <div>
          <label for="login-password" class="form-label">{{ t('auth.login.password') }}</label>
          <input id="login-password" v-model="form.password" name="password" type="password" required autocomplete="current-password" class="form-input" />
        </div>

        <button type="submit" class="btn-primary w-full" :disabled="auth.loginLoading">
          <svg v-if="auth.loginLoading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
          </svg>
          {{ auth.loginLoading ? t('auth.login.submitting') : t('auth.login.submit') }}
        </button>
      </form>

      <p class="mt-5 text-center text-sm text-slate-500">
        {{ t('auth.login.noAccount') }}
        <RouterLink :to="{ name: 'register' }" class="font-semibold text-brand-700 hover:text-brand-800">
          {{ t('auth.login.createAccount') }}
        </RouterLink>
      </p>
    </div>
  </div>
</template>
