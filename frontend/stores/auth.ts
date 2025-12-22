import { defineStore } from 'pinia'

interface User {
  id: number
  name?: string
  buyer_name?: string
  username?: string
  email?: string
  role?: string
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: null as string | null,
    isAdmin: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
  },

  actions: {
    setUser(user: User) {
      this.user = user
      // Persist user data
      if (process.client) {
        localStorage.setItem('auth_user', JSON.stringify(user))
        // Check if user is admin based on role or username presence
        const isAdmin = !!(user.role || (user.username && !user.email))
        this.isAdmin = isAdmin
        localStorage.setItem('auth_isAdmin', String(isAdmin))
      }
    },

    setToken(token: string) {
      this.token = token
      // Store in localStorage for persistence
      if (process.client) {
        localStorage.setItem('auth_token', token)
      }
    },

    setIsAdmin(isAdmin: boolean) {
      this.isAdmin = isAdmin
      if (process.client) {
        localStorage.setItem('auth_isAdmin', String(isAdmin))
      }
    },

    logout() {
      this.user = null
      this.token = null
      this.isAdmin = false
      if (process.client) {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_isAdmin')
      }
    },

    // Initialize from localStorage on app start
    init() {
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        const userStr = localStorage.getItem('auth_user')
        const isAdminStr = localStorage.getItem('auth_isAdmin')
        
        if (token) {
          this.token = token
        }
        
        if (userStr) {
          try {
            this.user = JSON.parse(userStr)
          } catch (e) {
            console.error('Failed to parse user from localStorage:', e)
          }
        }
        
        if (isAdminStr) {
          this.isAdmin = isAdminStr === 'true'
        }
      }
    }
  }
})

