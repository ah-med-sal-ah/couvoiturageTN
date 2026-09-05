import { computed, unref } from 'vue'

/**
 * Evaluates a password against the app's five requirements (more than 8
 * characters, uppercase, lowercase, number, symbol) and derives a simple
 * weak/medium/strong rating from how many are satisfied.
 */
export function usePasswordStrength(passwordRef) {
  const criteria = computed(() => {
    const value = unref(passwordRef) || ''
    return {
      length: value.length > 8,
      uppercase: /[A-Z]/.test(value),
      lowercase: /[a-z]/.test(value),
      number: /[0-9]/.test(value),
      symbol: /[^A-Za-z0-9]/.test(value),
    }
  })

  const score = computed(() => Object.values(criteria.value).filter(Boolean).length)

  const level = computed(() => {
    if (!unref(passwordRef)) return null
    if (score.value >= 5) return 'strong'
    if (score.value >= 3) return 'medium'
    return 'weak'
  })

  const colorClass = computed(() => {
    switch (level.value) {
      case 'strong':
        return 'bg-emerald-500'
      case 'medium':
        return 'bg-orange-500'
      case 'weak':
        return 'bg-passenger-500'
      default:
        return 'bg-slate-200'
    }
  })

  const isValid = computed(() => score.value === 5)

  return { criteria, score, level, colorClass, isValid }
}
