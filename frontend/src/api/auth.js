import { http } from '@/lib/http'

export function login(payload) {
  return http.post('/login', payload)
}

export function register(payload) {
  const formData = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value !== null && value !== undefined) {
      formData.append(key, value)
    }
  })

  return http.post('/register', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
}

export function logout() {
  return http.post('/logout')
}

export function fetchCurrentUser() {
  return http.get('/user')
}
