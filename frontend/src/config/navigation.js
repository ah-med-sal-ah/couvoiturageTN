/**
 * Single source of truth for the app's navigation entries, consumed by both
 * the mobile bottom nav / hidden menu and the desktop sidebar so the two
 * surfaces never drift out of sync.
 */
export const primaryNavItems = [
  { key: 'home', icon: 'home', labelKey: 'nav.home', routeName: 'home' },
  { key: 'passenger', icon: 'passenger', labelKey: 'nav.passenger', routeName: 'passenger' },
  { key: 'addPost', icon: 'plus', labelKey: 'nav.addPost', action: 'create-publication' },
  { key: 'driver', icon: 'conducteur', labelKey: 'nav.driver', routeName: 'driver' },
  { key: 'profile', icon: 'profile', labelKey: 'nav.profile', routeName: 'profile' },
]

export const secondaryNavItems = [
  { key: 'history', icon: 'history', labelKey: 'nav.history', routeName: 'history' },
  { key: 'help', icon: 'support', labelKey: 'nav.help', action: 'help' },
  { key: 'guidelines', icon: 'guidelines', labelKey: 'nav.guidelines', routeName: 'guidelines' },
  // Administrator-only - filtered in by useNavigationActions() based on the
  // authenticated user, not hidden by CSS (see EnsureUserIsAdmin backend
  // middleware for the actual authorization boundary).
  { key: 'dashboard', icon: 'dashboard', labelKey: 'nav.dashboard', routeName: 'admin-dashboard', adminOnly: true },
  { key: 'logout', icon: 'logout', labelKey: 'nav.logout', action: 'logout' },
]
