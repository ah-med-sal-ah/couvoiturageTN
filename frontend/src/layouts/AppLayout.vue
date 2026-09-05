<script setup>
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'

import CreatePublicationModal from '@/components/publications/CreatePublicationModal.vue'
import InfoModal from '@/components/layout/InfoModal.vue'
import MobileBottomNav from '@/components/layout/MobileBottomNav.vue'
import MobileHeader from '@/components/layout/MobileHeader.vue'
import MobileMenu from '@/components/layout/MobileMenu.vue'
import Sidebar from '@/components/layout/Sidebar.vue'

const route = useRoute()
const { t } = useI18n()
</script>

<template>
  <div class="min-h-screen bg-slate-50 lg:ps-64">
    <Sidebar />
    <MobileHeader />
    <MobileMenu />

    <main class="mx-auto max-w-6xl px-4 pb-24 pt-5 sm:px-6 lg:px-8 lg:pb-10 lg:pt-8">
      <h1 v-if="route.meta.titleKey" class="mb-5 text-xl font-semibold text-slate-900">
        {{ t(route.meta.titleKey) }}
      </h1>
      <RouterView />
    </main>

    <MobileBottomNav />
    <CreatePublicationModal />
    <InfoModal />
  </div>
</template>
