import { http } from '@/lib/http'

/**
 * Update the authenticated user's profile.
 *
 * Sent as multipart POST with a Laravel method-spoofing field because PHP
 * does not populate file uploads for native PUT requests.
 */
export function updateProfile(payload) {
  const formData = new FormData()
  formData.append('_method', 'PUT')
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      formData.append(key, value)
    }
  })

  return http.post('/user/profile', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
}

export function updatePassword(payload) {
  return http.put('/user/password', payload)
}
