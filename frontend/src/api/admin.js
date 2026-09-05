import { http } from '@/lib/http'

export function fetchDashboardStats() {
  return http.get('/admin/dashboard')
}
