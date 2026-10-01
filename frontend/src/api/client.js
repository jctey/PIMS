import { clearAuth, getToken } from '../composables/useAuth'

async function request(path, options = {}) {
  const headers = {
    Accept: 'application/json',
    ...(options.body ? { 'Content-Type': 'application/json' } : {}),
    ...(options.headers || {}),
  }

  const token = getToken()
  if (token) {
    headers.Authorization = `Bearer ${token}`
  }

  const response = await fetch(`/api${path}`, {
    ...options,
    headers,
  })

  if (response.status === 401) {
    clearAuth()
  }

  const text = await response.text()
  const data = text ? JSON.parse(text) : null

  if (!response.ok) {
    const message =
      data?.message ||
      (data?.errors ? Object.values(data.errors).flat().join(' ') : null) ||
      `Request failed (${response.status})`
    const error = new Error(message)
    error.status = response.status
    error.data = data
    throw error
  }

  return data
}

export const api = {
  login(payload) {
    return request('/login', {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
  getProducts(params = {}) {
    const query = new URLSearchParams()
    Object.entries(params).forEach(([key, value]) => {
      if (value !== '' && value !== null && value !== undefined) {
        query.set(key, value)
      }
    })
    const qs = query.toString()
    return request(`/products${qs ? `?${qs}` : ''}`)
  },
  getProduct(id) {
    return request(`/products/${id}`)
  },
}
