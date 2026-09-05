<script setup>
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'

import AppIcon from '@/components/common/AppIcon.vue'
import { primaryNavItems } from '@/config/navigation'
import { useNavigationActions } from '@/composables/useNavigationActions'

const { t } = useI18n()
const route = useRoute()
const { activate } = useNavigationActions()

function isActive(item) {
  return item.routeName && route.name === item.routeName
}
</script>

<template>
  <nav
    class="fixed inset-x-0 bottom-0 z-30 flex items-stretch border-t border-slate-200 bg-white pb-[env(safe-area-inset-bottom)] lg:hidden"
    aria-label="Primary"
  >
    <button
      v-for="item in primaryNavItems"
      :key="item.key"
      type="button"
      class="flex flex-1 flex-col items-center justify-center gap-1 py-2 text-[11px] font-medium"
      :class="isActive(item) ? 'text-brand-700' : 'text-slate-500'"
      @click="activate(item)"
    >
      <span v-if="item.key === 'addPost'" class="-mt-7 drop-shadow-md">
        <AppIcon name="plus" raw :size="46" />
      </span>
      <AppIcon v-else :name="item.icon" :size="22" />
      <span :class="item.key === 'addPost' ? '-mt-1' : ''">{{ t(item.labelKey) }}</span>
    </button>
  </nav>
</template>
