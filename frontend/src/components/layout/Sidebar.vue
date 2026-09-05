<script setup>
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'

import AppIcon from '@/components/common/AppIcon.vue'
import { useNavigationActions } from '@/composables/useNavigationActions'
import { primaryNavItems } from '@/config/navigation'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const route = useRoute()
const auth = useAuthStore()
const { activate, secondaryNavItems } = useNavigationActions()

function isActive(item) {
  return item.routeName && route.name === item.routeName
}
</script>

<template>
  <aside class="fixed inset-y-0 start-0 z-20 hidden w-64 flex-col border-e border-slate-200 bg-white lg:flex">
    <div class="flex items-center gap-2.5 border-b border-slate-200 px-6 py-5">
      <img src="/images/couvoiturage.png" alt="CovoiturageTN" class="h-9 w-9 rounded-lg object-cover" />
      <span class="text-lg font-semibold tracking-tight text-slate-900">{{ t('app.name') }}</span>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
      <button
        v-for="item in primaryNavItems"
        :key="item.key"
        type="button"
        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
        :class="isActive(item) ? 'bg-brand-50 text-brand-800' : 'text-slate-700 hover:bg-slate-50'"
        @click="activate(item)"
      >
        <AppIcon :name="item.icon" :size="20" />
        {{ t(item.labelKey) }}
      </button>

      <hr class="my-3 border-slate-100" />

      <button
        v-for="item in secondaryNavItems"
        :key="item.key"
        type="button"
        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
        :class="item.key === 'logout' ? 'text-passenger-600 hover:bg-passenger-50' : 'text-slate-700 hover:bg-slate-50'"
        @click="activate(item)"
      >
        <AppIcon :name="item.icon" :size="20" />
        {{ t(item.labelKey) }}
      </button>
    </nav>

    <RouterLink :to="{ name: 'profile' }" class="flex items-center gap-3 border-t border-slate-200 px-4 py-4 hover:bg-slate-50">
      <img
        v-if="auth.user?.profile_photo_url"
        :src="auth.user.profile_photo_url"
        alt=""
        class="h-10 w-10 rounded-full object-cover"
      />
      <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-800">
        <AppIcon name="profile" :size="18" />
      </div>
      <div class="min-w-0">
        <p class="truncate text-sm font-semibold text-slate-900">{{ auth.user?.full_name }}</p>
        <p class="truncate text-xs text-slate-500" dir="ltr">@{{ auth.user?.username }}</p>
      </div>
    </RouterLink>
  </aside>
</template>
