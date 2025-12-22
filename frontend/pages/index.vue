<script setup lang="ts">
import { onMounted } from 'vue'

// Redirect logic - same as backend
const { isAuthenticated, isAdmin } = useAuth()
const router = useRouter()

// Redirect based on auth status (same as backend web.php)
onMounted(async () => {
  // Wait a bit for auth state to initialize
  await new Promise(resolve => setTimeout(resolve, 100))
  
  if (isAuthenticated.value) {
    if (isAdmin.value) {
      await router.push('/admin/dashboard')
    } else {
      await router.push('/shop')
    }
  } else {
    await router.push('/auth/login')
  }
})
</script>

<template>
  <div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mb-4"></div>
      <p class="text-gray-600">Redirecting...</p>
    </div>
  </div>
</template>
