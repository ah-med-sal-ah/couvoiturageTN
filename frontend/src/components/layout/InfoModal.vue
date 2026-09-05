<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

import BaseModal from '@/components/common/BaseModal.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import { useUiStore } from '@/stores/ui'

const { t } = useI18n()
const ui = useUiStore()

const config = computed(() => {
  if (ui.infoModal === 'help') {
    return { icon: 'support', title: t('menu.helpTitle'), body: t('menu.helpBody') }
  }
  return null
})
</script>

<template>
  <BaseModal v-if="config" :title="config.title" size="sm" @close="ui.closeInfoModal()">
    <div class="flex flex-col items-center gap-3 py-6 text-center">
      <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-700">
        <AppIcon :name="config.icon" :size="26" />
      </div>
      <p class="text-sm text-slate-600">{{ config.body }}</p>
    </div>
  </BaseModal>
</template>
