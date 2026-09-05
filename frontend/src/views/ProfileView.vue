<script setup>
import { useI18n } from 'vue-i18n'

import LanguageSelector from '@/components/profile/LanguageSelector.vue'
import PasswordForm from '@/components/profile/PasswordForm.vue'
import ProfileForm from '@/components/profile/ProfileForm.vue'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const auth = useAuthStore()
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-6">
    <div v-if="auth.user?.is_admin" class="flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-2.5 text-sm font-medium text-brand-800">
      {{ t('profile.administrator') }}
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ t('profile.personalInfo') }}</h2>
      <ProfileForm />
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ t('profile.language') }}</h2>
      <LanguageSelector />
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
      <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-slate-500">{{ t('profile.security') }}</h2>
      <PasswordForm />
    </section>
  </div>
</template>
