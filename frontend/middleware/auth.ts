export default defineNuxtRouteMiddleware((to, from) => {
  const { isAuthenticated } = useAuth()
  const authStore = useAuthStore()

  // Ensure store is initialized from localStorage first
  if (process.client && (!authStore.token || !authStore.user)) {
    authStore.init()
  }

  // Check if we have token and user (direct check to avoid flash)
  const hasToken = authStore.token || (process.client && localStorage.getItem('auth_token'))
  const hasUser = authStore.user || (process.client && localStorage.getItem('auth_user'))

  // Only redirect if we truly don't have authentication
  if (!hasToken || !hasUser) {
    if (!isAuthenticated.value) {
      return navigateTo('/auth/login')
    }
  }
})

