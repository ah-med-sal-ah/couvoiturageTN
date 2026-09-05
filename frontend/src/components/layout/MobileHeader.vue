<script setup>
import { useI18n } from 'vue-i18n'

import { useUiStore } from '@/stores/ui'

const { t } = useI18n()
const ui = useUiStore()
</script>

<template>
  <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
    <button
      type="button"
      class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100"
      :aria-label="t('nav.menu')"
      @click="ui.openMobileMenu()"
    >
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
      </svg>
    </button>
    <div class="flex items-center gap-2">
      <img src="/images/couvoiturage.png" alt="" class="h-7 w-7 rounded-md object-cover" />
      <span class="text-sm font-semibold text-slate-900">{{ t('app.name') }}</span>
    </div>
    <span class="w-9" />
  </header>
</template>
