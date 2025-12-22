<template>
  <div class="max-w-md mx-auto">
    <div class="bg-white p-6 rounded shadow">
      <h3 class="text-lg font-semibold mb-4">Enter the 6-digit code</h3>
      <p class="text-sm text-gray-600 mb-4">We sent a code to <strong>{{ email }}</strong>. Check your inbox.</p>
      <form @submit.prevent="handleVerify" class="space-y-4">
        <input type="hidden" name="email" :value="email">
        <div>
          <input 
            v-model="code"
            name="code" 
            maxlength="6" 
            minlength="6" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 text-center tracking-widest text-xl" 
            placeholder="______" 
            required
          />
          <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
        </div>
        <Button type="submit" :disabled="loading">
          Verify Code
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { PasswordResetResponse } from '~/types/api'

definePageMeta({
  middleware: 'guest',
  title: 'Verify Code'
})

const route = useRoute()
const router = useRouter()
const { api } = useApi()

const email = route.query.email as string || ''
const code = ref('')
const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleVerify = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await api<PasswordResetResponse>('/password/otp/verify', {
      method: 'POST',
      body: {
        email,
        code: code.value
      }
    })

    if (response.status === 'success' && response.token) {
      await router.push(`/auth/password/new?token=${response.token}&email=${encodeURIComponent(email)}`)
    } else {
      errors.value = { general: response.message || 'Verification failed' }
    }
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || error.message || 'An error occurred' }
    }
  } finally {
    loading.value = false
  }
}
</script>

