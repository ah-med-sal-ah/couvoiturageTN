import { http } from '@/lib/http'

export function fetchPublications(params = {}) {
  return http.get('/publications', { params })
}

export function fetchPublication(id) {
  return http.get(`/publications/${id}`)
}

export function createPublication(payload) {
  return http.post('/publications', payload)
}

export function updateReservation(publicationId, reservationEnabled) {
  return http.patch(`/publications/${publicationId}/reservation`, {
    reservation_enabled: reservationEnabled,
  })
}
