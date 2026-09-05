<script setup>
import { computed, toRef } from 'vue'
import { useI18n } from 'vue-i18n'

import { usePasswordStrength } from '@/composables/usePasswordStrength'

const props = defineProps({
  password: { type: String, default: '' },
})

const { t } = useI18n()
const { score, level, colorClass } = usePasswordStrength(toRef(props, 'password'))

const label = computed(() => (level.value ? t(`password.strength.${level.value}`) : ''))
const textColorClass = computed(() => {
  switch (level.value) {
    case 'strong':
      return 'text-emerald-600'
    case 'medium':
      return 'text-orange-600'
    case 'weak':
      return 'text-passenger-600'
    default:
      return 'text-slate-400'
  }
})
</script>

<template>
  <div v-if="password" class="mt-2">
    <div class="flex gap-1">
      <span
        v-for="i in 5"
        :key="i"
        class="h-1.5 flex-1 rounded-full transition-colors"
        :class="i <= score ? colorClass : 'bg-slate-200'"
      />
    </div>
    <p class="mt-1 text-xs font-medium" :class="textColorClass">
      {{ t('password.strength.label') }}: {{ label }}
    </p>
  </div>
</template>
