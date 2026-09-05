import { createRouter, createWebHistory } from 'vue-router'

import { useAuthStore } from '@/stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: { guestOnly: true },
  },
  {
    path: '/',
    component: () => import('@/layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/HomeView.vue'),
        meta: { titleKey: 'nav.home' },
      },
      {
        path: 'passenger',
        name: 'passenger',
        component: () => import('@/views/PassengerView.vue'),
        meta: { titleKey: 'nav.passenger' },
      },
      {
        path: 'driver',
        name: 'driver',
        component: () => import('@/views/DriverView.vue'),
        meta: { titleKey: 'nav.driver' },
      },
      {
        path: 'profile',
        name: 'profile',
        component: () => import('@/views/ProfileView.vue'),
        meta: { titleKey: 'nav.profile' },
      },
      {
        path: 'history',
        name: 'history',
        component: () => import('@/views/HistoryView.vue'),
        meta: { titleKey: 'nav.history' },
      },
      {
        path: 'guidelines',
        name: 'guidelines',
        component: () => import('@/views/GuidelinesView.vue'),
        meta: { titleKey: 'nav.guidelines' },
      },
      {
        path: 'admin/dashboard',
        name: 'admin-dashboard',
        component: () => import('@/views/admin/DashboardView.vue'),
        meta: { titleKey: 'nav.dashboard', requiresAdmin: true },
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  // Reloading directly on a deep route (e.g. /history) fires this guard as
  // soon as the router is installed, which can race auth.initialize()'s
  // token check on app boot. Awaiting the same (memoized) call here makes
  // sure isAuthenticated always reflects a real, resolved check.
  await auth.initialize()

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // UX-only: hides the admin route from a normal user who types the URL
  // directly. The actual authorization boundary is the backend's `admin`
  // middleware - the dashboard's own API call is rejected server-side even
  // if this check were ever bypassed.
  if (to.meta.requiresAdmin && !auth.user?.is_admin) {
    return { name: 'home' }
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return { name: 'home' }
  }

  return true
})

export default router
