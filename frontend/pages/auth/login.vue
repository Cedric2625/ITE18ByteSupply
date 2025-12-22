<template>
  <div class="max-w-md mx-auto">
    <form @submit.prevent="handleLogin" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <!-- Email/Username Field -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
          Email or Username
        </label>
        <input
          id="email"
          v-model="form.email"
          type="text"
          placeholder="Enter your email or username"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email }}
        </p>
      </div>

      <!-- Password Field -->
      <div>
        <Label for="password" value="Password" />
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
          />
          <button 
            type="button" 
            @click="showPassword = !showPassword" 
            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
            aria-label="Toggle password visibility"
          >
            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
          </button>
        </div>
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password }}
        </p>
      </div>

      <!-- Google OAuth Error Message (from query params) -->
      <div v-if="oauthError" class="bg-red-100 border-2 border-red-400 text-red-800 px-4 py-3 rounded-md shadow-md mb-4">
        <div class="flex items-start">
          <i class="fas fa-exclamation-circle mr-2 text-lg mt-0.5"></i>
          <div class="flex-1">
            <p class="font-semibold">{{ oauthError }}</p>
            <div v-if="oauthEmail" class="mt-2">
              <p class="text-sm mb-2">Would you like to register with this email?</p>
              <NuxtLink 
                :to="`/auth/register?email=${encodeURIComponent(oauthEmail)}`"
                class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700"
              >
                <i class="fas fa-user-plus mr-2"></i> Register Now
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>

      <!-- Success Message (from logout or other redirects) -->
      <div v-if="route.query.success" class="bg-green-100 border-2 border-green-400 text-green-800 px-4 py-3 rounded-md shadow-md mb-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <i class="fas fa-check-circle mr-2 text-lg"></i>
            <span class="font-semibold">{{ decodeURIComponent(route.query.success as string) }}</span>
          </div>
          <button @click="clearSuccessMessage" class="text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errors.general" class="bg-red-100 border-2 border-red-400 text-red-800 px-4 py-3 rounded-md shadow-md">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2 text-lg"></i>
          <span class="font-semibold">{{ errors.general }}</span>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between">
        <NuxtLink 
          to="/auth/register" 
          class="text-sm text-indigo-600 hover:text-indigo-800"
        >
          Don't have an account? Sign Up
        </NuxtLink>
        <button 
          type="submit" 
          :disabled="loading"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-gray-800 hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          LOGIN
        </button>
      </div>

      <!-- Footer Links -->
      <div class="flex items-center justify-between">
        <NuxtLink 
          to="/auth/password/forgot" 
          class="text-sm text-gray-600 hover:text-gray-800"
        >
          Forgot Password?
        </NuxtLink>
        <a 
          :href="`http://127.0.0.1:8001/oauth/google?frontend=true&redirect=${encodeURIComponent('http://localhost:3000/shop')}`" 
          class="inline-flex items-center bg-white border px-3 py-2 rounded shadow text-sm"
        >
          <i class="fab fa-google text-red-500 mr-2"></i> Sign in with Google
        </a>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import type { Router } from 'vue-router'

definePageMeta({
  middleware: 'guest',
  title: 'Login'
})

const { login } = useAuth()
const router = useRouter() as Router
const route = useRoute()

const form = reactive({
  email: '',
  password: ''
})

const showPassword = ref(false)
const loading = ref(false)
const errors = ref<Record<string, string>>({})

// Handle Google OAuth errors from query params
const oauthError = ref<string>('')
const oauthEmail = ref<string>('')

const clearSuccessMessage = () => {
  router.replace({ query: { ...route.query, success: undefined } })
}

onMounted(() => {
  // Check for success message (from logout or other redirects)
  if (route.query.success) {
    // Auto-clear success message after 5 seconds
    setTimeout(() => {
      router.replace({ query: { ...route.query, success: undefined } })
    }, 5000)
  }

  // Check for error from Google OAuth redirect
  if (route.query.error) {
    oauthError.value = decodeURIComponent(route.query.error as string)
    
    // Extract email from error message if provided
    if (route.query.email) {
      oauthEmail.value = decodeURIComponent(route.query.email as string)
      form.email = oauthEmail.value
    } else {
      // Try to extract email from error message
      const emailMatch = oauthError.value.match(/([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/)
      if (emailMatch) {
        oauthEmail.value = emailMatch[1]
        form.email = oauthEmail.value
      }
    }
    
    // Clean up URL
    router.replace({ query: { ...route.query, error: undefined, email: undefined } })
  }
})

const handleLogin = async () => {
  loading.value = true
  errors.value = {}

  try {
    const result = await login(form.email, form.password)

    if (result.success) {
      // Check user_type from response to determine redirect
      const userType = result.data?.user_type || (result.data?.admin_info ? 'admin' : 'buyer')
      
      if (userType === 'admin') {
        await router.push('/admin/dashboard')
      } else {
        await router.push('/shop')
      }
    } else {
      errors.value = { general: result.error || 'Login failed' }
      console.error('Login error:', result.error)
    }
  } catch (error: any) {
    console.error('Login exception:', error)
    const errorMessage = error.data?.message || error.message || error.statusMessage || 'An error occurred'
    errors.value = { general: errorMessage }
  } finally {
    loading.value = false
  }
}
</script>
