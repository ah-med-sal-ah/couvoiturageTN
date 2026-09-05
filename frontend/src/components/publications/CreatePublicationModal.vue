<script setup>
import { useI18n } from 'vue-i18n'

import BaseModal from '@/components/common/BaseModal.vue'
import PublicationForm from '@/components/publications/PublicationForm.vue'
import { useUiStore } from '@/stores/ui'

const { t } = useI18n()
const ui = useUiStore()

function onCreated() {
  ui.closeCreatePublication()
  ui.notifyPublicationCreated()
}
</script>

<template>
  <BaseModal v-if="ui.isCreatePublicationOpen" :title="t('publication.create.title')" size="lg" @close="ui.closeCreatePublication()">
    <PublicationForm :default-status="ui.createPublicationDefaultStatus" @created="onCreated" @cancel="ui.closeCreatePublication()" />
  </BaseModal>
</template>
