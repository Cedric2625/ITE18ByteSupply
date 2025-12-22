<template>
  <div class="bg-white rounded-lg shadow p-4 space-y-6">
    <!-- Error Message -->
    <div v-if="errors.general" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ errors.general }}</span>
      <button @click="errors.general = ''" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Success Message -->
    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ successMessage }}</span>
    </div>

    <div v-if="cart.length === 0" class="text-center py-8">
      <p class="text-gray-500 mb-4">Your cart is empty.</p>
      <NuxtLink to="/shop" class="text-indigo-600 hover:text-indigo-800">
        Continue Shopping
      </NuxtLink>
    </div>

    <div v-else>
      <div>
        <h3 class="text-lg font-semibold mb-2">Order Summary</h3>
        <ul class="space-y-1 text-sm">
          <li v-for="item in cart" :key="item.id">
            {{ item.name }} × {{ item.quantity }} — ${{ formatCurrency(item.price * item.quantity) }}
          </li>
        </ul>
        <div class="mt-2 font-bold">Total: ${{ formatCurrency(total) }}</div>
      </div>

      <form @submit.prevent="handleFormSubmit" class="space-y-4">
        <div>
          <Label for="payment_method" value="Payment Method" />
          <select 
            id="payment_method" 
            name="payment_method"
            v-model="form.payment_method"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300" 
            required
            :disabled="loading"
          >
            <option value="cash">Cash</option>
            <option value="credit_card">Credit Card</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="online_payment">Online Payment</option>
          </select>
        </div>
        <Button 
          type="submit" 
          :disabled="loading || cart.length === 0"
          @click.prevent="handleFormSubmit"
        >
          {{ loading ? 'Placing Order...' : 'Place Order' }}
        </Button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

const { api } = useApi()
const router = useRouter()
const { flashSuccess, flashError } = useFlash()

const form = reactive({
  payment_method: 'cash'
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})
const successMessage = ref('')

// Get cart from localStorage
const cart = ref<any[]>([])

onMounted(() => {
  if (process.client) {
    // Load cart from localStorage
    try {
      const stored = localStorage.getItem('cart')
      if (stored) {
        cart.value = JSON.parse(stored)
      }
    } catch (error) {
      console.error('Error loading cart from localStorage:', error)
      cart.value = []
    }
    
    // If cart is empty, redirect back to cart page after a delay
    if (cart.value.length === 0) {
      setTimeout(() => {
        router.push('/shop/cart')
      }, 500)
    }
  }
})

const total = computed(() => {
  return cart.value.reduce((sum: number, item: any) => {
    return sum + (item.price * item.quantity)
  }, 0)
})

// Watch for cart changes - redirect if cart becomes empty (but not on initial load)
watch(cart, (newCart, oldCart) => {
  // Only redirect if cart was not empty before and becomes empty
  if (oldCart && oldCart.length > 0 && newCart.length === 0 && process.client) {
    router.push('/shop/cart')
  }
})

// Handle form submission
const handleFormSubmit = (e?: Event) => {
  if (e) {
    e.preventDefault()
    e.stopPropagation()
  }
  console.log('Form submit triggered')
  placeOrder()
}

const placeOrder = async () => {
  console.log('=== PLACE ORDER CALLED ===')
  console.log('placeOrder function called')
  console.log('Cart length:', cart.value.length)
  console.log('Cart items:', cart.value)
  
  if (cart.value.length === 0) {
    console.warn('Cart is empty!')
    errors.value = { general: 'Your cart is empty. Please add items to your cart before checking out.' }
    return
  }

  loading.value = true
  errors.value = {}
  successMessage.value = ''
  
  console.log('Loading set to true, starting API call...')

  try {
    console.log('=== CHECKOUT DEBUG ===')
    console.log('Cart items:', cart.value)
    
    // Validate cart items have required fields
    const validCart = cart.value.filter((item: any) => {
      if (!item.id || !item.quantity) {
        console.warn('Invalid cart item:', item)
        return false
      }
      return true
    })

    if (validCart.length === 0) {
      throw new Error('No valid items in cart. Please add items to your cart.')
    }

    // Prepare order data for shop API - match backend API expectations
    const orderData = {
      payment_method: form.payment_method,
      items: validCart.map((item: any) => ({
        component_id: parseInt(item.id), // Ensure it's an integer
        quantity: parseInt(item.quantity) // Ensure it's an integer
      }))
    }

    console.log('Order data to send:', JSON.stringify(orderData, null, 2))
    console.log('API endpoint: /shop/place')
    console.log('Method: POST')

    const response = await api<any>('/shop/place', {
      method: 'POST',
      body: orderData
    })

    console.log('=== API RESPONSE ===')
    console.log('Full response:', JSON.stringify(response, null, 2))
    console.log('Response type:', typeof response)
    console.log('Response status:', response?.status)
    console.log('Response order:', response?.order)
    console.log('Response order id:', response?.order?.id)

    // Backend returns: { status: 'success', message: '...', order: { id: ..., ... } }
    // Handle the response - check multiple possible formats
    const responseStatus = response?.status || response?.data?.status
    const order = response?.order || response?.data?.order || response?.data
    
    console.log('Extracted status:', responseStatus)
    console.log('Extracted order:', order)
    console.log('Order ID:', order?.id)

    // Check if order was created successfully
    if (responseStatus === 'success' && order) {
      const orderId = order.id || order.order_id
      
      if (orderId) {
        console.log('✅ Order placed successfully! Order ID:', orderId)
        
        // Clear cart from localStorage
        if (process.client) {
          localStorage.removeItem('cart')
          cart.value = []
        }
        
        // Show success message
        flashSuccess('Order placed successfully!')
        successMessage.value = 'Order placed successfully! Redirecting...'
        
        // Redirect to order confirmation after a short delay
        setTimeout(async () => {
          await router.push(`/shop/orders/${orderId}?success=${encodeURIComponent('Order placed successfully!')}`)
        }, 1500)
      } else {
        console.error('❌ Order created but no ID found:', order)
        throw new Error('Order was created but could not retrieve order ID. Please check your orders.')
      }
    } else {
      console.error('❌ Invalid response format:', response)
      const errorMsg = response?.message || response?.data?.message || 'Failed to place order. Please try again.'
      throw new Error(errorMsg)
    }
  } catch (error: any) {
    console.error('=== CHECKOUT ERROR ===')
    console.error('Error object:', error)
    console.error('Error status:', error.status || error.statusCode)
    console.error('Error data:', error.data)
    console.error('Error message:', error.message)
    
    let errorMessage = 'Failed to place order. Please try again.'
    
    // Handle validation errors
    if (error.data?.errors) {
      const validationErrors = error.data.errors
      console.error('Validation errors:', validationErrors)
      
      if (typeof validationErrors === 'object') {
        const errorMessages: string[] = []
        Object.keys(validationErrors).forEach(key => {
          const msg = Array.isArray(validationErrors[key]) 
            ? validationErrors[key][0] 
            : validationErrors[key]
          errors.value[key] = msg
          errorMessages.push(`${key}: ${msg}`)
        })
        errorMessage = errorMessages.join('. ') || errorMessage
      }
    } else if (error.data?.message) {
      errorMessage = error.data.message
      console.error('API error message:', errorMessage)
      
      // Check for stock-related errors
      if (errorMessage.toLowerCase().includes('insufficient stock') || 
          errorMessage.toLowerCase().includes('stock')) {
        errorMessage = `⚠️ ${errorMessage}\n\nPlease remove items with insufficient stock from your cart and try again.`
      }
    } else if (error.message) {
      errorMessage = error.message
      console.error('General error message:', errorMessage)
      
      // Check for stock-related errors
      if (errorMessage.toLowerCase().includes('insufficient stock') || 
          errorMessage.toLowerCase().includes('stock')) {
        errorMessage = `⚠️ ${errorMessage}\n\nPlease remove items with insufficient stock from your cart and try again.`
      }
    }
    
    // Handle specific error cases
    if (error.status === 401 || error.statusCode === 401) {
      errorMessage = 'You are not authenticated. Please login again.'
      router.push('/auth/login')
    } else if (error.status === 422 || error.statusCode === 422) {
      errorMessage = error.data?.message || 'Validation error. Please check your cart items.'
    } else if (error.status === 400 || error.statusCode === 400) {
      // 400 Bad Request often means stock issues
      errorMessage = error.data?.message || 'Invalid request. Please check your cart items and stock availability.'
    } else if (error.status === 500 || error.statusCode === 500) {
      errorMessage = error.data?.message || 'Server error. Please try again later.'
    }
    
    errors.value = { general: errorMessage }
    flashError(errorMessage)
  } finally {
    loading.value = false
  }
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

