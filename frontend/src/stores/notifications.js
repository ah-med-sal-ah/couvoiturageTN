import { defineStore } from 'pinia'
import { ref } from 'vue'

let nextId = 1

export const useNotificationStore = defineStore('notifications', () => {
  const toasts = ref([])

  function push(message, type = 'error', duration = 5000) {
    const id = nextId++
    toasts.value.push({ id, message, type })

    if (duration > 0) {
      setTimeout(() => dismiss(id), duration)
    }

    return id
  }

  function dismiss(id) {
    toasts.value = toasts.value.filter((toast) => toast.id !== id)
  }

  const error = (message) => push(message, 'error')
  const success = (message) => push(message, 'success')
  const info = (message) => push(message, 'info')

  return { toasts, push, dismiss, error, success, info }
})
