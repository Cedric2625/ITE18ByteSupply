import type { ApiResponse } from '~/types/api'

export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase

  const api = async <T = any>(url: string, options: any = {}): Promise<T> => {
    const authStore = useAuthStore()
    const token = authStore.token

    const headers: Record<string, string> = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...options.headers
    }

    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    try {
      const method = options.method || 'GET'
      
      // Handle body - only for methods that support it
      let body = undefined
      if (options.body && method !== 'GET' && method !== 'DELETE') {
        // For PUT/PATCH/POST, stringify if not already a string
        body = typeof options.body === 'string' ? options.body : JSON.stringify(options.body)
      } else if (options.body && method === 'DELETE' && typeof options.body === 'object') {
        // DELETE might have query params, but not body
        body = undefined
      }
      
      const fetchOptions: any = {
        method,
        headers
      }
      
      // Only add body if it exists
      if (body !== undefined) {
        fetchOptions.body = body
      }
      
      // Log API calls in development
      if (process.dev) {
        console.log('🌐 API Call:', {
          method: method,
          url: `${apiBase}${url}`,
          fullUrl: `${apiBase}${url}`
        })
      }
      
      const response = await $fetch<ApiResponse<T>>(`${apiBase}${url}`, fetchOptions)
      
      // Log successful responses in development
      if (process.dev) {
        console.log('✅ API Response:', {
          method: method,
          url: `${apiBase}${url}`,
          data: response
        })
      }
      
      return response as T
    } catch (error: any) {
      console.error('API Error:', {
        url: `${apiBase}${url}`,
        method: options.method || 'GET',
        error: error.data || error.message || error
      })
      
      // Handle 401 unauthorized
      if (error.status === 401 || error.statusCode === 401) {
        authStore.logout()
        if (process.client) {
          navigateTo('/auth/login')
        }
      }
      throw error
    }
  }

  return { api, apiBase }
}

