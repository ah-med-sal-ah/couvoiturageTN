import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'

import { secondaryNavItems as allSecondaryNavItems } from '@/config/navigation'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

/**
 * Resolves a navigation item (from src/config/navigation.js) into the
 * concrete behaviour it triggers, shared by every nav surface (mobile
 * bottom bar, hidden menu, desktop sidebar) so the logic lives in one place.
 *
 * Also filters `secondaryNavItems` down to what the current user should
 * see (e.g. Dashboard, admin-only) - one place for both Sidebar and
 * MobileMenu to consume so they never drift out of sync. This is a UX
 * convenience only; the real authorization boundary is server-side
 * (EnsureUserIsAdmin), never this filter.
 */
export function useNavigationActions() {
  const router = useRouter()
  const ui = useUiStore()
  const auth = useAuthStore()
  const { t } = useI18n()

  const secondaryNavItems = computed(() =>
    allSecondaryNavItems.filter((item) => !item.adminOnly || auth.user?.is_admin),
  )

  function activate(item) {
    if (item.routeName) {
      router.push({ name: item.routeName })
      ui.closeMobileMenu()
      return
    }

    switch (item.action) {
      case 'create-publication':
        ui.openCreatePublication('passenger')
        break
      case 'help':
        ui.openInfoModal('help')
        break
      case 'logout':
        ui.closeMobileMenu()
        if (window.confirm(t('auth.logoutConfirm'))) {
          auth.logout().then(() => router.push({ name: 'login' }))
        }
        break
      default:
        break
    }
  }

  return { activate, secondaryNavItems }
}
