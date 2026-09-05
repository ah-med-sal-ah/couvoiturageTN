<script setup>
import { onMounted, onUnmounted } from 'vue'

const props = defineProps({
  title: { type: String, default: '' },
  size: { type: String, default: 'md' }, // sm | md | lg
})

const emit = defineEmits(['close'])

const sizes = {
  sm: 'max-w-sm',
  md: 'max-w-lg',
  lg: 'max-w-2xl',
}

function onKeydown(event) {
  if (event.key === 'Escape') emit('close')
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 p-0 sm:items-center sm:p-4" @mousedown.self="emit('close')">
      <div
        class="flex max-h-[92vh] w-full flex-col overflow-hidden rounded-t-2xl bg-white shadow-xl sm:rounded-2xl"
        :class="sizes[props.size]"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <h2 class="text-base font-semibold text-slate-900">{{ props.title }}</h2>
          <button
            type="button"
            class="rounded-md p-1 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
            @click="emit('close')"
          >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
              <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
          </button>
        </div>
        <div class="overflow-y-auto px-5 py-4">
          <slot />
        </div>
        <div v-if="$slots.footer" class="border-t border-slate-200 px-5 py-4">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>
