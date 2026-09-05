<script setup>
import { useNotificationStore } from '@/stores/notifications'

const notifications = useNotificationStore()

const styles = {
  error: 'border-passenger-200 bg-white text-passenger-700',
  success: 'border-emerald-200 bg-white text-emerald-700',
  info: 'border-brand-200 bg-white text-brand-700',
}

const dotStyles = {
  error: 'bg-passenger-500',
  success: 'bg-emerald-500',
  info: 'bg-brand-500',
}
</script>

<template>
  <div class="pointer-events-none fixed inset-x-0 top-3 z-[100] flex flex-col items-center gap-2 px-4 sm:top-4 sm:items-end sm:px-6">
    <TransitionGroup name="toast">
      <div
        v-for="toast in notifications.toasts"
        :key="toast.id"
        class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-lg border px-4 py-3 shadow-card"
        :class="styles[toast.type] || styles.info"
        role="alert"
      >
        <span class="mt-1 h-2 w-2 shrink-0 rounded-full" :class="dotStyles[toast.type] || dotStyles.info" />
        <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
        <button
          type="button"
          class="text-slate-400 hover:text-slate-600"
          @click="notifications.dismiss(toast.id)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
          </svg>
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.2s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
