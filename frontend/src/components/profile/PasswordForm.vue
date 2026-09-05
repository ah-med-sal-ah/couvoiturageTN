<script setup>
import { computed, reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import PasswordStrengthMeter from '@/components/common/PasswordStrengthMeter.vue'
import { extractErrorMessage, isGloballyHandled } from '@/lib/http'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

const { t } = useI18n()
const auth = useAuthStore()
const notifications = useNotificationStore()

const form = reactive({ password: '', password_confirmation: '' })
const errors = ref({})
const saving = ref(false)

const passwordsMatch = computed(() => !form.password_confirmation || form.password === form.password_confirmation)

function fieldError(field) {
  return errors.value?.[field]?.[0] || ''
}

async function submit() {
  errors.value = {}

  if (form.password !== form.password_confirmation) {
    errors.value = { password_confirmation: [t('auth.register.passwordMismatch')] }
    return
  }

  saving.value = true
  try {
    await auth.updatePassword(form)
    notifications.success(t('profile.passwordUpdated'))
    form.password = ''
    form.password_confirmation = ''
  } catch (error) {
    if (error?.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else if (!isGloballyHandled(error)) {
      notifications.error(extractErrorMessage(error))
    }
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <form class="space-y-5" @submit.prevent="submit">
    <div>
      <label class="form-label">{{ t('profile.newPassword') }}</label>
      <input
        v-model="form.password"
        type="password"
        autocomplete="new-password"
        class="form-input"
        :class="{ '!border-passenger-400': fieldError('password') }"
      />
      <PasswordStrengthMeter :password="form.password" />
      <p class="mt-1 text-xs text-slate-400">{{ t('password.requirements') }}</p>
      <p v-if="fieldError('password')" class="form-error">{{ fieldError('password') }}</p>
    </div>

    <div>
      <label class="form-label">{{ t('profile.repeatPassword') }}</label>
      <input
        v-model="form.password_confirmation"
        type="password"
        autocomplete="new-password"
        class="form-input"
        :class="{ '!border-passenger-400': !passwordsMatch || fieldError('password_confirmation') }"
      />
      <p v-if="!passwordsMatch" class="form-error">{{ t('auth.register.passwordMismatch') }}</p>
      <p v-else-if="fieldError('password_confirmation')" class="form-error">{{ fieldError('password_confirmation') }}</p>
    </div>

    <div class="flex justify-end border-t border-slate-100 pt-4">
      <button type="submit" class="btn-primary" :disabled="saving || !form.password">
        <svg v-if="saving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        {{ t('profile.changePassword') }}
      </button>
    </div>
  </form>
</template>
