<template>
  <div class="max-w-md mx-auto">
    <form @submit.prevent="handleRegister" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <!-- Name Field -->
      <div>
        <label for="buyer_name" class="block text-sm font-medium text-gray-700">Name</label>
        <input
          id="buyer_name"
          v-model="form.buyer_name"
          type="text"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required
        />
        <p v-if="errors.buyer_name" class="mt-1 text-sm text-red-600">
          {{ errors.buyer_name }}
        </p>
      </div>

      <!-- Email Field -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          required
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email }}
        </p>
      </div>

      <!-- Password Field -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
            @input="checkPasswordStrength"
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
        <!-- Password Strength Indicator -->
        <div v-if="form.password.length > 0" class="mt-2">
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
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password }}
        </p>
      </div>

      <!-- Confirm Password Field -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <div class="relative">
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="showConfirm ? 'text' : 'password'"
            class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
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
        <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
          {{ errors.password_confirmation }}
        </p>
      </div>

      <!-- Success Modal -->
      <AlertModal
        :show="showSuccessModal"
        title="Registration Successful"
        message="You have successfully registered! Please login to continue."
        type="info"
        @close="handleSuccessModalClose"
      />

      <!-- Error Message -->
      <div v-if="errors.general" class="bg-red-100 border-2 border-red-400 text-red-800 px-4 py-3 rounded-md shadow-md">
        <div class="flex items-center">
          <i class="fas fa-exclamation-circle mr-2 text-lg"></i>
          <span class="font-semibold">{{ errors.general }}</span>
        </div>
      </div>

    <!-- Email Taken Modal -->
    <AlertModal
      :show="showEmailTakenModal"
      title="Email Already Taken"
      message="This email is already registered. Please use a different email or try logging in instead."
      type="error"
      @close="showEmailTakenModal = false"
    />

      <!-- Actions -->
      <div class="flex items-center justify-between">
        <button 
          type="submit" 
          :disabled="loading"
          class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Registering...' : 'Register' }}
        </button>
        <NuxtLink 
          to="/auth/login" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Back to Login
        </NuxtLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'guest',
  title: 'Register'
})

const { register } = useAuth()
const router = useRouter()

const route = useRoute()

const form = reactive({
  buyer_name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const showPassword = ref(false)
const showConfirm = ref(false)
const loading = ref(false)
const errors = ref<Record<string, string>>({})
const showSuccessModal = ref(false)
const showEmailTakenModal = ref(false)
const passwordStrength = ref<'weak' | 'medium' | 'strong' | ''>('')
const passwordStrengthText = ref('')

// Pre-fill email if coming from Google OAuth error
onMounted(() => {
  if (route.query.email) {
    form.email = decodeURIComponent(route.query.email as string)
    // Clean up URL
    router.replace({ query: { ...route.query, email: undefined } })
  }
})

// Password strength checker
const checkPasswordStrength = () => {
  const password = form.password
  
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
  // User stays on registration page - they can navigate to login manually
}

const handleRegister = async () => {
  loading.value = true
  errors.value = {}
  showSuccessModal.value = false

  // Validate passwords match
  if (form.password !== form.password_confirmation) {
    errors.value.password_confirmation = 'Passwords do not match'
    loading.value = false
    return
  }

  // Validate password length
  if (form.password.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
    loading.value = false
    return
  }

  // Validate name length
  if (form.buyer_name.length < 4) {
    errors.value.buyer_name = 'Name must be at least 4 characters'
    loading.value = false
    return
  }

  try {
    const result = await register(form.buyer_name, form.email, form.password)

    if (result.success) {
      // Clear form
      form.buyer_name = ''
      form.email = ''
      form.password = ''
      form.password_confirmation = ''
      passwordStrength.value = ''
      passwordStrengthText.value = ''
      
      // Show success modal
      showSuccessModal.value = true
    } else {
      // Handle validation errors
      if (result.errors) {
        // Set field-specific errors
        Object.keys(result.errors).forEach(key => {
          const errorMsg = Array.isArray(result.errors[key]) ? result.errors[key][0] : result.errors[key]
          if (key === 'email' && (errorMsg.includes('taken') || errorMsg.includes('already'))) {
            errors.value.email = 'This email is already registered. Please login instead or use a different email.'
            showEmailTakenModal.value = true
          } else {
            errors.value[key] = errorMsg
          }
        })
      }
      
      // Set general error if not already set
      if (!errors.value.general && !showEmailTakenModal.value) {
        errors.value.general = result.error || 'Registration failed. Please check your information and try again.'
      }
    }
  } catch (error: any) {
    console.error('Registration error:', error)
    
    // Handle validation errors (422)
    if (error.status === 422 || error.statusCode === 422) {
      const validationErrors = error.data?.errors || {}
      
      // Convert Laravel validation errors to flat object
      Object.keys(validationErrors).forEach(key => {
        const errorMsg = Array.isArray(validationErrors[key]) ? validationErrors[key][0] : validationErrors[key]
        
        if (key === 'email' && (errorMsg.includes('taken') || errorMsg.includes('already'))) {
          errors.value.email = 'This email is already registered. Please login instead or use a different email.'
          showEmailTakenModal.value = true
        } else {
          errors.value[key] = errorMsg
        }
      })
      
      // Set general error if not already set
      if (!errors.value.general && !showEmailTakenModal.value) {
        errors.value.general = error.data?.message || 'Validation failed. Please check your information and try again.'
      }
    } else {
      // Other errors
      errors.value.general = error.data?.message || error.message || 'An error occurred. Please try again later.'
    }
  } finally {
    loading.value = false
  }
}
</script>

