import { http } from '@/lib/http'

export function fetchLocations(search = '') {
  return http.get('/locations', { params: search ? { search } : {} })
}
