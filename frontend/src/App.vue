<script setup>
import { useAuthStore } from '@/stores/auth'
import ToastContainer from '@/components/common/ToastContainer.vue'

const auth = useAuthStore()
</script>

<template>
  <div v-if="auth.initializing" class="flex h-screen items-center justify-center bg-slate-50">
    <div class="h-8 w-8 animate-spin rounded-full border-2 border-brand-600 border-t-transparent" />
  </div>
  <RouterView v-else />
  <ToastContainer />
</template>
