import { createPinia } from 'pinia'
import { createApp } from 'vue'

import App from '@/App.vue'
import { applyLocale, i18n } from '@/i18n'
import { onHttpEvent } from '@/lib/http'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

import '@/style.css'

// Set the initial lang/dir attributes before the first paint.
applyLocale(i18n.global.locale.value)

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.use(i18n)

const notifications = useNotificationStore()
const auth = useAuthStore()

onHttpEvent('unauthorized', () => {
  if (auth.isAuthenticated || auth.token) {
    auth.clearSession()
    notifications.error(i18n.global.t('errors.unauthorized'))
    router.push({ name: 'login' })
  }
})
onHttpEvent('serverError', () => notifications.error(i18n.global.t('errors.generic')))
onHttpEvent('network', () => notifications.error(i18n.global.t('errors.network')))

// The router's own guard awaits this same call (see router/index.js), so it
// never judges auth.isAuthenticated before the token has actually been
// checked - regardless of whether its first navigation resolves before or
// after this finishes.
auth.initialize().finally(() => {
  app.mount('#app')
})
