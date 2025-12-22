<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Option A: Use old password -->
    <div class="bg-blue-100 p-6 rounded">
      <h3 class="text-lg font-semibold mb-4">Reset using your old password</h3>
      <form @submit.prevent="handleOldPasswordReset" class="space-y-4">
        <div>
          <Label for="email" value="Email (Customer)" />
          <input 
            id="email"
            v-model="oldPasswordForm.email"
            type="email" 
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
            required
          />
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
        </div>
        <div>
          <Label for="old_password" value="Old password" />
          <div class="relative">
            <input 
              id="old_password"
              v-model="oldPasswordForm.old_password"
              :type="showOld ? 'text' : 'password'" 
              class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
              required
            />
            <button 
              type="button" 
              @click="showOld = !showOld" 
              class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
              aria-label="Toggle old password visibility"
            >
              <i class="fas" :class="showOld ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
          </div>
          <p v-if="errors.old_password" class="mt-1 text-sm text-red-600">{{ errors.old_password }}</p>
        </div>
        <div>
          <Label for="password" value="New password" />
          <div class="relative">
            <input 
              id="password"
              v-model="oldPasswordForm.password"
              :type="showNew ? 'text' : 'password'" 
              class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
              required
              @input="checkPasswordStrength"
            />
            <button 
              type="button" 
              @click="showNew = !showNew" 
              class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
              aria-label="Toggle new password visibility"
            >
              <i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
          </div>
          <!-- Password Strength Indicator -->
          <div v-if="oldPasswordForm.password.length > 0" class="mt-2">
            <div class="flex items-center space-x-2">
              <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div 
                  :class="[
                    'h-2 rounded-full transition-all duration-300',
                    passwordStrength === 'weak' ? 'bg-red-500 w-1/3' :
                    passwordStrength === 'medium' ? 'bg-yellow-500 w-2/3' :
                    passwordStrength === 'strong' ? 'bg-green-500 w-full' : 'bg-gray-300'
                  ]"
                ></div>
              </div>
              <span 
                :class="[
                  'text-xs font-medium',
                  passwordStrength === 'weak' ? 'text-red-600' :
                  passwordStrength === 'medium' ? 'text-yellow-600' :
                  passwordStrength === 'strong' ? 'text-green-600' : 'text-gray-500'
                ]"
              >
                {{ passwordStrengthText }}
              </span>
            </div>
          </div>
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>
        <div>
          <Label for="password_confirmation" value="Confirm password" />
          <div class="relative">
            <input 
              id="password_confirmation"
              v-model="oldPasswordForm.password_confirmation"
              :type="showConfirm ? 'text' : 'password'" 
              class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2"
              required
            />
            <button 
              type="button" 
              @click="showConfirm = !showConfirm" 
              class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
              aria-label="Toggle confirm password visibility"
            >
              <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
          </div>
        </div>
        <Button type="submit" :disabled="loading">
          Confirm Reset Password
        </Button>
      </form>
    </div>

    <!-- Success Modal -->
    <AlertModal
      :show="showSuccessModal"
      title="Password Reset Successful"
      message="You Have Successfully Changed Your Password."
      type="info"
      @close="handleSuccessModalClose"
    />

    <!-- Option B: Sign in with Google (email OTP) -->
    <div class="bg-blue-100 p-6 rounded">
      <h3 class="text-lg font-semibold mb-4">Or sign in with Google</h3>
      <p class="text-sm text-gray-600 mb-4">Enter the Gmail connected to your account. We'll send a 6-digit code to continue.</p>
      <div class="space-y-4">
        <p class="text-sm text-gray-600">Choose your Google account. We'll send a 6-digit code to that Gmail.</p>
        <a 
          href="http://127.0.0.1:8001/oauth/google/reset" 
          class="bg-white border px-4 py-2 rounded shadow inline-flex items-center"
        >
          <i class="fab fa-google text-red-500 mr-2"></i> Sign in with Google
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import type { PasswordResetResponse } from '~/types/api'

definePageMeta({
  middleware: 'guest',
  title: 'Reset Password'
})

const router = useRouter()
const { api } = useApi()

const oldPasswordForm = reactive({
  email: '',
  old_password: '',
  password: '',
  password_confirmation: ''
})

const showOld = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)
const loading = ref(false)
const errors = ref<Record<string, string>>({})
const passwordStrength = ref<'weak' | 'medium' | 'strong' | ''>('')
const passwordStrengthText = ref('')
const showSuccessModal = ref(false)

// Password strength checker
const checkPasswordStrength = () => {
  const password = oldPasswordForm.password
  
  if (password.length === 0) {
    passwordStrength.value = ''
    passwordStrengthText.value = ''
    return
  }
  
  let strength = 0
  
  // Length check
  if (password.length >= 8) strength++
  if (password.length >= 12) strength++
  
  // Character variety checks
  if (/[a-z]/.test(password)) strength++ // lowercase
  if (/[A-Z]/.test(password)) strength++ // uppercase
  if (/[0-9]/.test(password)) strength++ // numbers
  if (/[^a-zA-Z0-9]/.test(password)) strength++ // special characters
  
  // Determine strength level
  if (strength <= 2) {
    passwordStrength.value = 'weak'
    passwordStrengthText.value = 'Weak'
  } else if (strength <= 4) {
    passwordStrength.value = 'medium'
    passwordStrengthText.value = 'Medium'
  } else {
    passwordStrength.value = 'strong'
    passwordStrengthText.value = 'Strong'
  }
}

const handleSuccessModalClose = () => {
  showSuccessModal.value = false
  // Redirect to login page after closing modal
  router.push('/auth/login?success=' + encodeURIComponent('Password reset successfully'))
}

const handleOldPasswordReset = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await api<PasswordResetResponse>('/password/old', {
      method: 'POST',
      body: oldPasswordForm
    })

    if (response.status === 'success') {
      // Clear form
      oldPasswordForm.email = ''
      oldPasswordForm.old_password = ''
      oldPasswordForm.password = ''
      oldPasswordForm.password_confirmation = ''
      passwordStrength.value = ''
      passwordStrengthText.value = ''
      
      // Show success modal
      showSuccessModal.value = true
    } else {
      errors.value = { general: response.message || 'Password reset failed' }
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

