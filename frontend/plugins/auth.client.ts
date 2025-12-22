export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Initialize auth from localStorage synchronously
  // This must complete before middleware runs to prevent redirect flash
  authStore.init()
  
  // Try to fetch user if token exists (in background, non-blocking)
  if (authStore.token && !authStore.user) {
    const { fetchUser } = useAuth()
    // Fetch user in background - don't await to avoid blocking page load
    fetchUser().catch((error) => {
      // If fetch fails, clear invalid token
      console.error('Failed to fetch user:', error)
      authStore.logout()
    })
  }
})

