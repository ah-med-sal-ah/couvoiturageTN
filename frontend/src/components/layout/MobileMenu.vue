<script setup>
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'

import AppIcon from '@/components/common/AppIcon.vue'
import { useNavigationActions } from '@/composables/useNavigationActions'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

const { t } = useI18n()
const route = useRoute()
const ui = useUiStore()
const auth = useAuthStore()
const { activate, secondaryNavItems } = useNavigationActions()

const homeItem = { key: 'home', icon: 'home', labelKey: 'nav.home', routeName: 'home' }
const profileItem = { key: 'profile', icon: 'profile', labelKey: 'nav.profile', routeName: 'profile' }

function isActive(item) {
  return item.routeName && route.name === item.routeName
}
</script>

<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="ui.isMobileMenuOpen" class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" @click="ui.closeMobileMenu()" />
    </Transition>
    <Transition name="slide">
      <aside
        v-if="ui.isMobileMenuOpen"
        class="fixed inset-y-0 start-0 z-50 flex w-72 max-w-[85vw] flex-col bg-white shadow-xl lg:hidden"
        aria-label="Menu"
      >
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
          <div class="flex items-center gap-2.5">
            <img src="/images/couvoiturage.png" alt="CovoiturageTN" class="h-9 w-9 rounded-lg object-cover" />
            <span class="font-semibold text-slate-900">{{ t('app.name') }}</span>
          </div>
          <button type="button" class="rounded-md p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600" @click="ui.closeMobileMenu()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
              <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
          </button>
        </div>

        <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
          <img
            v-if="auth.user?.profile_photo_url"
            :src="auth.user.profile_photo_url"
            alt=""
            class="h-11 w-11 rounded-full object-cover"
          />
          <div v-else class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-100 text-brand-800">
            <AppIcon name="profile" :size="20" />
          </div>
          <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-slate-900">{{ auth.user?.full_name }}</p>
            <p class="truncate text-xs text-slate-500" dir="ltr">@{{ auth.user?.username }}</p>
          </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
          <button
            v-for="item in [profileItem, homeItem]"
            :key="item.key"
            type="button"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"
            :class="isActive(item) ? 'bg-brand-50 text-brand-800' : 'text-slate-700 hover:bg-slate-50'"
            @click="activate(item)"
          >
            <AppIcon :name="item.icon" :size="20" />
            {{ t(item.labelKey) }}
          </button>

          <hr class="my-2 border-slate-100" />

          <button
            v-for="item in secondaryNavItems"
            :key="item.key"
            type="button"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium"
            :class="item.key === 'logout' ? 'text-passenger-600 hover:bg-passenger-50' : 'text-slate-700 hover:bg-slate-50'"
            @click="activate(item)"
          >
            <AppIcon :name="item.icon" :size="20" />
            {{ t(item.labelKey) }}
          </button>
        </nav>
      </aside>
    </Transition>
  </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.25s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
html[dir='rtl'] .slide-enter-from,
html[dir='rtl'] .slide-leave-to {
  transform: translateX(100%);
}
</style>
