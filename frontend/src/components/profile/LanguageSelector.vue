<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { SUPPORTED_LOCALES } from '@/i18n'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

const { locale, t } = useI18n()
const auth = useAuthStore()
const notifications = useNotificationStore()
const saving = ref(false)

async function selectLanguage(code) {
  if (code === locale.value || saving.value) return
  saving.value = true
  try {
    await auth.setLanguage(code)
  } catch {
    notifications.error(t('errors.generic'))
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <div class="grid grid-cols-3 gap-2">
    <button
      v-for="code in SUPPORTED_LOCALES"
      :key="code"
      type="button"
      class="rounded-lg border px-3 py-2.5 text-sm font-medium transition-colors"
      :class="
        code === locale
          ? 'border-brand-600 bg-brand-50 text-brand-800'
          : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'
      "
      :disabled="saving"
      @click="selectLanguage(code)"
    >
      {{ t(`languageNames.${code}`) }}
    </button>
  </div>
</template>
