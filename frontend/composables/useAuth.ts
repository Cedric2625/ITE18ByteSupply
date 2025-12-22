import { computed } from 'vue'
import { useAuthStore } from '~/stores/auth'
import type { LoginResponse, RegisterResponse } from '~/types/api'

export const useAuth = () => {
  const authStore = useAuthStore()
  const { api } = useApi()

  const login = async (email: string, password: string) => {
    try {
      const response = await api<LoginResponse & { user_type?: string; admin_info?: any }>('/login', {
        method: 'POST',
        body: {
          email,
          password
        }
      })

      console.log('Login response:', response)

      if (response.status === 'success' && response.token) {
        // Handle admin login
        if (response.user_type === 'admin' && response.admin_info) {
          authStore.setUser({
            id: response.admin_info.id,
            username: response.admin_info.username,
            role: response.admin_info.role
          })
          authStore.setToken(response.token)
          authStore.setIsAdmin(true)
          return { success: true, data: response }
        }
        
        // Handle buyer login
        if (response.user_type === 'buyer' && response.user_info) {
          authStore.setUser(response.user_info)
          authStore.setToken(response.token)
          authStore.setIsAdmin(false)
          return { success: true, data: response }
        }
        
        // Fallback: check if admin_info exists (for backward compatibility)
        if (response.admin_info) {
          authStore.setUser({
            id: response.admin_info.id,
            username: response.admin_info.username,
            role: response.admin_info.role
          })
          authStore.setToken(response.token)
          authStore.setIsAdmin(true)
          return { success: true, data: response }
        }
        
        // Fallback: check if user_info exists (for backward compatibility)
        if (response.user_info) {
          authStore.setUser(response.user_info)
          authStore.setToken(response.token)
          authStore.setIsAdmin(false)
          return { success: true, data: response }
        }
      }

      return { success: false, error: response.message || 'Login failed' }
    } catch (error: any) {
      console.error('Login catch error:', error)
      const errorMsg = error.data?.message || error.message || error.statusMessage || 'Login failed'
      return { success: false, error: errorMsg }
    }
  }

  const register = async (name: string, email: string, password: string) => {
    try {
      const response = await api<RegisterResponse>('/register', {
        method: 'POST',
        body: {
          name,
          email,
          password
        }
      })

      console.log('Register response:', response)

      if (response.status === 'success') {
        // Don't automatically log in - user needs to login manually
        return { success: true, data: response, message: response.message || 'Account created successfully!' }
      }
      
      return { success: false, error: response.message || 'Registration failed' }
    } catch (error: any) {
      console.error('Register catch error:', error)
      
      // Handle validation errors (422)
      if (error.status === 422 || error.statusCode === 422) {
        const validationErrors = error.data?.errors || {}
        const errorMessages: Record<string, string> = {}
        
        // Convert Laravel validation errors to flat object
        Object.keys(validationErrors).forEach(key => {
          if (Array.isArray(validationErrors[key])) {
            errorMessages[key] = validationErrors[key][0]
          } else {
            errorMessages[key] = validationErrors[key]
          }
        })
        
        // Check for duplicate email - provide clear message
        if (errorMessages.email) {
          const emailError = Array.isArray(errorMessages.email) ? errorMessages.email[0] : errorMessages.email
          const isDuplicate = emailError.includes('taken') || emailError.includes('already')
          
          return { 
            success: false, 
            error: isDuplicate 
              ? 'This email is already registered. Please login instead or use a different email.'
              : emailError,
            errors: errorMessages
          }
        }
        
        return { 
          success: false, 
          error: error.data?.message || 'Validation failed',
          errors: errorMessages
        }
      }
      
      const errorMsg = error.data?.message || error.message || error.statusMessage || 'Registration failed'
      return { success: false, error: errorMsg }
    }
  }

  const adminLogin = async (username: string, password: string) => {
    try {
      const response = await api<LoginResponse>('/admin/login', {
        method: 'POST',
        body: {
          username,
          password
        }
      })

      console.log('Admin login response:', response)

      if (response.status === 'success' && response.token && response.admin_info) {
        // Set admin info as user
        const adminInfo = {
          id: response.admin_info.id,
          username: response.admin_info.username,
          role: response.admin_info.role
        }
        authStore.setUser(adminInfo)
        authStore.setToken(response.token)
        authStore.setIsAdmin(true)
        return { success: true, data: response }
      }
      
      return { success: false, error: response.message || 'Admin login failed' }
    } catch (error: any) {
      console.error('Admin login catch error:', error)
      const errorMsg = error.data?.message || error.message || error.statusMessage || 'Admin login failed'
      return { success: false, error: errorMsg }
    }
  }

  const logout = async () => {
    try {
      await api('/logout', {
        method: 'POST'
      })
    } catch (error) {
      // Continue with logout even if API call fails
      console.error('Logout API error:', error)
    } finally {
      authStore.logout()
      // Show logout message on login page via query parameter
      navigateTo('/auth/login?success=' + encodeURIComponent('You have been logged out successfully.'))
    }
  }

  const fetchUser = async () => {
    try {
      const response = await api<{ user_info?: any }>('/get-user', {
        method: 'GET'
      })
      
      if (response.user_info) {
        const userInfo = response.user_info
        // Determine if user is admin based on response structure
        // Admin has 'username' and 'role', Buyer has 'email' and 'buyer_name'
        const isAdmin = !!(userInfo.role || (userInfo.username && !userInfo.email))
        
        authStore.setUser(userInfo)
        authStore.setIsAdmin(isAdmin)
        return { success: true, data: response }
      }
      
      return { success: false, error: 'Failed to fetch user' }
    } catch (error: any) {
      return { success: false, error: error.data?.message || 'Failed to fetch user' }
    }
  }

  return {
    login,
    register,
    adminLogin,
    logout,
    fetchUser,
    user: computed(() => authStore.user),
    isAuthenticated: computed(() => authStore.isAuthenticated),
    isAdmin: computed(() => authStore.isAdmin)
  }
}

