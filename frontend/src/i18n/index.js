import { createI18n } from 'vue-i18n'

import ar from '@/locales/ar.json'
import en from '@/locales/en.json'
import fr from '@/locales/fr.json'

export const SUPPORTED_LOCALES = ['en', 'fr', 'ar']
export const DEFAULT_LOCALE = 'en'
export const RTL_LOCALES = ['ar']
export const STORAGE_KEY = 'covoiturage_tn_locale'

/**
 * Resolve the locale to boot with: a previously saved preference (set from
 * the Profile page, or restored from the user's account), otherwise
 * English. The app's default language is English regardless of the
 * browser/OS language - it is only ever changed by an explicit user choice.
 */
function resolveInitialLocale() {
  try {
    const saved = localStorage.getItem(STORAGE_KEY)
    if (saved && SUPPORTED_LOCALES.includes(saved)) {
      return saved
    }
  } catch {
    // localStorage may be unavailable (private browsing, etc.) - ignore.
  }

  return DEFAULT_LOCALE
}

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: resolveInitialLocale(),
  fallbackLocale: DEFAULT_LOCALE,
  messages: { en, fr, ar },
})

/**
 * Apply a locale to the document (dir/lang attributes) and vue-i18n, and
 * persist the preference so it survives a refresh.
 */
export function applyLocale(locale) {
  if (!SUPPORTED_LOCALES.includes(locale)) {
    locale = DEFAULT_LOCALE
  }

  i18n.global.locale.value = locale

  const isRtl = RTL_LOCALES.includes(locale)
  document.documentElement.setAttribute('lang', locale)
  document.documentElement.setAttribute('dir', isRtl ? 'rtl' : 'ltr')

  try {
    localStorage.setItem(STORAGE_KEY, locale)
  } catch {
    // Ignore storage failures - the preference just won't survive a refresh.
  }
}
