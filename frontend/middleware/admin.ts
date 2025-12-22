export default defineNuxtRouteMiddleware(async (to, from) => {
  const { isAdmin, isAuthenticated, fetchUser } = useAuth()
  const authStore = useAuthStore()

  // First, check localStorage directly to avoid flash (runs synchronously)
  if (process.client) {
    const token = localStorage.getItem('auth_token')
    const userStr = localStorage.getItem('auth_user')
    const isAdminStr = localStorage.getItem('auth_isAdmin')
    
    // If we have data in localStorage but not in store, restore it immediately
    if (token && (!authStore.token || !authStore.user)) {
      authStore.setToken(token)
      if (userStr) {
        try {
          const user = JSON.parse(userStr)
          authStore.setUser(user)
        } catch (e) {
          console.error('Failed to parse user from localStorage:', e)
        }
      }
      if (isAdminStr) {
        authStore.setIsAdmin(isAdminStr === 'true')
      }
    }
    
    // If no data in localStorage at all, redirect immediately
    if (!token || !userStr) {
      if (!isAuthenticated.value) {
        return navigateTo('/auth/login')
      }
    }
  }

  // Ensure store is initialized
  if (!authStore.token || !authStore.user) {
    authStore.init()
  }

  // If we have token but no user, try to fetch user
  if (authStore.token && !authStore.user) {
    try {
      await fetchUser()
    } catch (error) {
      // If fetch fails, clear invalid token and redirect to login
      console.error('Failed to fetch user:', error)
      authStore.logout()
      return navigateTo('/auth/login')
    }
  }

  // Final authentication check
  if (!isAuthenticated.value) {
    return navigateTo('/auth/login')
  }

  // Final admin check
  if (!isAdmin.value) {
    return navigateTo('/shop')
  }
})

