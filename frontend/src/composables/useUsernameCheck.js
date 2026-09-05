import { reactive } from 'vue'

import { checkUsernameAvailability } from '@/api/username'

/**
 * Proactive username-availability check, shared by Register and Profile:
 * on blur, ask the backend whether the chosen username is free and, if
 * not, offer a ready-to-use suggestion. The actual uniqueness constraint
 * is still enforced server-side at submit time - this is only a head start
 * so the user doesn't have to guess-and-resubmit.
 */
export function useUsernameCheck() {
  const state = reactive({
    checking: false,
    available: null, // null = not checked yet
    suggestion: '',

    async check(username) {
      if (!username || username.length < 3) {
        state.available = null
        state.suggestion = ''
        return
      }

      state.checking = true
      try {
        const { data } = await checkUsernameAvailability(username)
        state.available = data.available
        state.suggestion = data.suggestion || ''
      } catch {
        // Not critical - submit-time validation remains the real guard.
        state.available = null
        state.suggestion = ''
      } finally {
        state.checking = false
      }
    },

    reset() {
      state.available = null
      state.suggestion = ''
    },
  })

  return state
}
