import { http } from '@/lib/http'

export function checkUsernameAvailability(username) {
  return http.get('/username-availability', { params: { username } })
}
