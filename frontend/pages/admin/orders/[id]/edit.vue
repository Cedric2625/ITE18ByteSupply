<template>
  <div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
      <NuxtLink 
        to="/admin/orders" 
        class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-2"
      >
        <i class="fas fa-arrow-left mr-2"></i>Back
      </NuxtLink>
      <h2 class="text-2xl font-semibold text-gray-900">Edit Order</h2>
    </div>

    <!-- Error Message -->
    <div v-if="errors.general" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>{{ errors.general }}</span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="orderPending && !order && !orderError" class="bg-white p-6 rounded-lg shadow text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
      <p class="mt-2 text-sm text-gray-500">Loading order data...</p>
    </div>

    <!-- Error Loading Order -->
    <div v-if="orderError && !order" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <div class="flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span>Failed to load order. Please try again.</span>
      </div>
    </div>

    <!-- Debug Info (Development Only) - Shows form loading status -->
    <div v-if="!order && !orderPending && !orderError" class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded text-sm">
      <p><strong>⚠️ Form Status:</strong></p>
      <p>Waiting for order data to load...</p>
    </div>

    <!-- CRITICAL: Force edit page to show - this should always be visible on edit page -->
    <div v-if="!order && !orderPending && !orderError" class="mb-4 bg-red-100 border-2 border-red-500 p-4 rounded">
      <p class="text-red-800 font-bold">⚠️ WARNING: Order data not loaded. Form may not work correctly.</p>
      <p class="text-sm mt-2">Order Pending: {{ orderPending }}</p>
      <p class="text-sm">Order Data: {{ orderData ? 'Exists' : 'Null' }}</p>
      <p class="text-sm">Order: {{ order ? 'Exists' : 'Null' }}</p>
    </div>

    <!-- Order Information Card (Read-Only) -->
    <div v-if="order" class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Order Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Order details and components.
          </p>
        </div>
      </div>
      <div class="border-t border-gray-200">
        <dl>
          <!-- Order reference -->
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Order reference
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ order?.order_reference_number || 'N/A' }}
            </dd>
          </div>
          
          <!-- Buyer -->
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Buyer
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div>
                <NuxtLink 
                  v-if="order?.buyer?.id"
                  :to="`/admin/buyers/${order.buyer.id}`" 
                  class="text-blue-600 hover:text-blue-900"
                >
                  {{ order?.buyer?.buyer_name || 'N/A' }}
                </NuxtLink>
                <span v-else>{{ order?.buyer?.buyer_name || 'N/A' }}</span>
              </div>
              <div class="text-gray-500">{{ order?.buyer?.buyer_number || '' }}</div>
            </dd>
          </div>
          
          <!-- Admin -->
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Admin
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div>{{ order?.admin?.username || 'admin' }}</div>
              <div class="text-gray-500">{{ order?.admin?.role || 'system_admin' }}</div>
            </dd>
          </div>
          
          <!-- Order details -->
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Order details
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="space-y-2">
                <div>
                  <span class="font-medium">Date:</span> {{ formatDateLong(order?.order_date) }}
                </div>
                <div>
                  <span class="font-medium">Payment Method:</span> {{ formatPaymentMethod(order?.payment_method) }}
                </div>
                <div>
                  <span class="font-medium">Total Amount:</span> ${{ formatCurrency(order?.total_amount || 0) }}
                </div>
              </div>
            </dd>
          </div>
          
          <!-- Shipping status - Editable -->
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Shipping status
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="space-y-4">
                <!-- Current Status Display -->
                <div>
                  <span class="text-xs text-gray-500 mb-2 block">Current Status:</span>
                  <span :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    getStatusClass(order?.shipping_status)
                  ]">
                    {{ formatStatus(order?.shipping_status) }}
                  </span>
                </div>
                
                <!-- Additional Info -->
                <div class="space-y-1 pt-2 border-t border-gray-200">
                  <div v-if="order?.tracking_number" class="text-sm">
                    <span class="font-medium">Tracking Number:</span> {{ order.tracking_number }}
                  </div>
                  <div v-if="order?.estimated_delivery" class="text-sm">
                    <span class="font-medium">Estimated Delivery:</span> {{ formatDateLong(order.estimated_delivery) }}
                  </div>
                </div>
              </div>
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Locked Order Message -->
    <div v-if="isOrderLocked" class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded">
      <div class="flex items-center">
        <i class="fas fa-lock mr-2"></i>
        <span class="font-semibold">This order cannot be edited.</span>
      </div>
      <p class="mt-2 text-sm">
        Orders with status "<strong>{{ formatStatus(order?.shipping_status) }}</strong>" cannot be modified. 
        Once an order is marked as <strong>Cancelled</strong> or <strong>Completed</strong>, it becomes locked and cannot be updated.
      </p>
    </div>

    <!-- Single Edit Form - Only Shipping Status is Editable -->
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow" :class="{ 'opacity-50 pointer-events-none': isOrderLocked }">
      <!-- Shipping Status - Only editable field -->
      <div>
        <Label for="shipping_status" value="Shipping Status *" />
        <div class="mt-1 relative">
          <select
            id="shipping_status"
            name="shipping_status"
            v-model="form.shipping_status"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900 py-2 px-3"
            required
            :disabled="loading || isOrderLocked"
          >
            <option value="" disabled>-- Select Shipping Status --</option>
            <option 
              value="pending"
              :disabled="isStatusDisabled('pending')"
            >
              Pending
            </option>
            <option 
              value="shipped"
              :disabled="isStatusDisabled('shipped')"
            >
              Shipped
            </option>
            <option 
              value="arrived_hub"
              :disabled="isStatusDisabled('arrived_hub')"
            >
              Arrived at Delivery Hub
            </option>
            <option 
              value="out_for_delivery"
              :disabled="isStatusDisabled('out_for_delivery')"
            >
              Out for Delivery
            </option>
            <option 
              value="completed"
              :disabled="isStatusDisabled('completed')"
            >
              Completed
            </option>
            <option 
              value="canceled"
              :disabled="isStatusDisabled('canceled')"
            >
              Canceled
            </option>
          </select>
        </div>
        <p v-if="errors.shipping_status" class="mt-1 text-sm text-red-600">
          {{ errors.shipping_status }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
        <NuxtLink 
          :to="`/admin/orders/${route.params.id}`" 
          class="inline-flex items-center px-6 py-3 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          CANCEL
        </NuxtLink>
        <Button 
          type="submit" 
          :disabled="loading || !form.shipping_status || isOrderLocked"
          class="px-6 py-3"
        >
          <i class="fas fa-save mr-2"></i>
          {{ loading ? 'UPDATING...' : isOrderLocked ? 'ORDER LOCKED' : 'UPDATE SHIPPING STATUS' }}
        </Button>
      </div>
    </form>

    <!-- Alert Modal for Backward Status Update -->
    <AlertModal
      :show="showBackwardStatusModal"
      title="Cannot Update Status Backward"
      message="You Can't Update its Status Backward. Once a status has been updated forward, it cannot be changed back to a previous status."
      type="error"
      @close="showBackwardStatusModal = false"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useFlash } from '~/composables/useFlash'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: ['auth', 'admin'],
  title: 'Edit Order',
  // Use a unique, specific key for edit routes to ensure correct routing
  key: (route: any) => `order-edit-${route.params.id}-edit-page`,
  // Explicitly set the route name to ensure Nuxt recognizes this as the edit route
  alias: []
})

const route = useRoute()
const { api } = useApi()
const router = useRouter()
const { flashSuccess, flashError } = useFlash()

// Debug: Verify we're on the edit page
if (typeof window !== 'undefined') {
  console.log('🔵 EDIT PAGE ROUTE:', {
    path: route.path,
    params: route.params,
    name: route.name,
    fullPath: route.fullPath,
    isEditRoute: route.path.includes('/edit'),
    componentName: 'edit.vue'
  })
  
  // Alert if we're on edit route but wrong page loaded
  if (route.path.includes('/edit')) {
    console.log('✅ Correctly on edit route')
  } else {
    console.error('❌ NOT on edit route! Current path:', route.path)
  }
}

// Fetch order data
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: orderData, pending: orderPending, error: orderError, refresh } = await useLazyAsyncData(`order-edit-${route.params.id}`, async () => {
  try {
    const response = await api(`/orders/${route.params.id}`, { method: 'GET' })
    return response
  } catch (error: any) {
    console.error('Failed to load order:', error)
    throw error
  }
})

const order = computed(() => {
  if (!orderData.value) return null
  return orderData.value?.data || orderData.value
})

const form = reactive({
  shipping_status: ''
})

// Populate form when order data loads
watch(order, (newOrder) => {
  if (newOrder) {
    console.log('Loading order data into form:', {
      shipping_status: newOrder.shipping_status
    })
    
    // Ensure shipping_status is always set and valid
    const validStatuses = ['pending', 'shipped', 'arrived_hub', 'out_for_delivery', 'completed', 'canceled']
    const status = (newOrder.shipping_status && validStatuses.includes(newOrder.shipping_status)) 
      ? newOrder.shipping_status 
      : 'pending'
    
    form.shipping_status = status
    // Store original status for comparison
    originalShippingStatus.value = status
    
    console.log('Form populated. Shipping status:', form.shipping_status)
    console.log('Original shipping status stored:', originalShippingStatus.value)
  }
}, { immediate: true })

// Watch for backward status changes in the form
watch(() => form.shipping_status, (newStatus, oldStatus) => {
  const currentStatus = originalShippingStatus.value || order.value?.shipping_status || ''
  
  // Only check if we have a current status and the new status is different
  if (currentStatus && newStatus && newStatus !== currentStatus && newStatus !== oldStatus) {
    if (isStatusBackward(currentStatus, newStatus)) {
      // Show modal and reset to current status
      showBackwardStatusModal.value = true
      // Reset to original status after a brief delay to allow modal to show
      setTimeout(() => {
        form.shipping_status = currentStatus
      }, 100)
    }
  }
})

// Removed separate shipping status handlers - now part of main form

const loading = ref(false)
const errors = ref<Record<string, string>>({})
const originalShippingStatus = ref<string>('')
const showBackwardStatusModal = ref(false)

// Check if order is locked (cancelled or completed)
const isOrderLocked = computed(() => {
  const status = order.value?.shipping_status || originalShippingStatus.value || ''
  return status === 'cancelled' || status === 'canceled' || status === 'completed'
})

// Define status progression order (higher number = later in progression)
const getStatusOrder = (status: string): number => {
  const statusOrder: Record<string, number> = {
    'pending': 0,
    'shipped': 1,
    'arrived_hub': 2,
    'out_for_delivery': 3,
    'completed': 4,
    'canceled': 5 // Special case - can be set from any status, but once set, shouldn't go back
  }
  return statusOrder[status] ?? -1
}

// Check if new status is backward from current status
const isStatusBackward = (currentStatus: string, newStatus: string): boolean => {
  // If current status is canceled or cancelled, only allow keeping it as canceled
  if (currentStatus === 'canceled' || currentStatus === 'cancelled') {
    return newStatus !== 'canceled'
  }
  
  // If current status is completed, cannot change to any other status
  if (currentStatus === 'completed') {
    return newStatus !== 'completed'
  }
  
  // If new status is canceled, it's allowed (can cancel from any status) unless already completed
  if (newStatus === 'canceled' && currentStatus !== 'completed') {
    return false
  }
  
  // Compare status order
  const currentOrder = getStatusOrder(currentStatus)
  const newOrder = getStatusOrder(newStatus)
  
  // If either status is invalid, don't block
  if (currentOrder === -1 || newOrder === -1) {
    return false
  }
  
  // New status is backward if its order is less than current order
  return newOrder < currentOrder
}

// Check if a status option should be disabled in the dropdown
const isStatusDisabled = (status: string): boolean => {
  const currentStatus = originalShippingStatus.value || order.value?.shipping_status || ''
  
  if (!currentStatus) {
    return false
  }
  
  // If order is locked (cancelled or completed), disable all options
  if (isOrderLocked.value) {
    return true
  }
  
  // If current status is canceled, disable all except canceled
  if (currentStatus === 'canceled' || currentStatus === 'cancelled') {
    return status !== 'canceled'
  }
  
  // If current status is completed, disable all options
  if (currentStatus === 'completed') {
    return true
  }
  
  // Canceled can always be selected (can cancel from any status) unless order is locked
  if (status === 'canceled') {
    return false
  }
  
  // Disable if the status is backward from current
  return isStatusBackward(currentStatus, status)
}

const formatCurrency = (amount: number) => {
  if (!amount) return '0.00'
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDateLong = (date: string | undefined) => {
  if (!date) return 'N/A'
  try {
    const d = new Date(date)
    return d.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch (e) {
    return 'N/A'
  }
}

const formatPaymentMethod = (method: string | undefined) => {
  if (!method) return 'N/A'
  return method.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatStatus = (status: string | undefined) => {
  if (!status) return 'N/A'
  const statusMap: Record<string, string> = {
    'pending': 'Waiting',
    'processing': 'Processing',
    'shipped': 'Shipped',
    'arrived_hub': 'Arrived at Delivery Hub',
    'out_for_delivery': 'Out for Delivery',
    'completed': 'Completed',
    'canceled': 'Canceled',
    'cancelled': 'Canceled'
  }
  return statusMap[status] || status.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const getStatusClass = (status: string | undefined) => {
  if (!status) return 'bg-gray-100 text-gray-800'
  const classes: Record<string, string> = {
    completed: 'bg-green-100 text-green-800',
    shipped: 'bg-blue-100 text-blue-800',
    out_for_delivery: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    arrived_hub: 'bg-yellow-100 text-yellow-800',
    canceled: 'bg-red-100 text-red-800',
    cancelled: 'bg-red-100 text-red-800',
    processing: 'bg-blue-100 text-blue-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Removed handleStatusUpdate - now using single handleSubmit function for all updates

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    // Check if order is locked (cancelled or completed)
    if (isOrderLocked.value) {
      errors.value = { general: 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.' }
      loading.value = false
      return
    }

    // Validate required fields
    if (!form.shipping_status || form.shipping_status === '') {
      errors.value = { shipping_status: 'Please select a shipping status.' }
      loading.value = false
      return
    }

    // Check if trying to update status backward
    const currentStatus = originalShippingStatus.value || order.value?.shipping_status || ''
    if (currentStatus && isStatusBackward(currentStatus, form.shipping_status)) {
      showBackwardStatusModal.value = true
      loading.value = false
      // Reset form to original status
      form.shipping_status = currentStatus
      return
    }

    // Prevent updating to cancelled or completed if already locked
    if (currentStatus === 'cancelled' || currentStatus === 'canceled' || currentStatus === 'completed') {
      errors.value = { general: 'This order cannot be edited. Orders with status "Cancelled" or "Completed" are locked and cannot be modified.' }
      loading.value = false
      return
    }

    // Prepare the request body - only shipping_status
    const requestBody: any = {
      shipping_status: form.shipping_status
    }

    console.log('Updating order shipping status:', {
      orderId: route.params.id,
      currentStatus: currentStatus,
      newStatus: form.shipping_status
    })

    const response = await api(`/orders/${route.params.id}`, {
      method: 'PATCH',
      body: requestBody
    })
    
    console.log('Order update response:', response)
    
    // Update original status after successful update
    originalShippingStatus.value = form.shipping_status
    
    flashSuccess(`Order updated successfully. Shipping status changed to: ${formatStatus(form.shipping_status)}`)
    await router.push('/admin/orders')
  } catch (error: any) {
    console.error('Failed to update order:', error)
    // Handle validation errors
    if (error.data?.errors) {
      const validationErrors: Record<string, string> = {}
      Object.keys(error.data.errors).forEach(key => {
        if (Array.isArray(error.data.errors[key])) {
          validationErrors[key] = error.data.errors[key][0]
        } else {
          validationErrors[key] = error.data.errors[key]
        }
      })
      errors.value = validationErrors
    } else {
      const errorMessage = error.data?.message || error.message || 'Failed to update order'
      errors.value = { general: errorMessage }
      flashError(errorMessage)
    }
  } finally {
    loading.value = false
  }
}
</script>
