<template>
  <div class="max-w-4xl mx-auto">
    <!-- Success/Error Messages -->
    <div v-if="route.query.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ route.query.success }}</span>
      <button @click="clearMessage" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div v-if="route.query.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <span class="block sm:inline">{{ route.query.error }}</span>
      <button @click="clearMessage" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <!-- Order Information Card -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
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
              {{ order?.order_reference_number || order?.data?.order_reference_number }}
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
                  v-if="order?.buyer?.id || order?.data?.buyer?.id"
                  :to="`/admin/buyers/${order?.buyer?.id || order?.data?.buyer?.id}`" 
                  class="text-blue-600 hover:text-blue-900"
                >
                  {{ order?.buyer?.buyer_name || order?.data?.buyer?.buyer_name || 'N/A' }}
                </NuxtLink>
                <span v-else>{{ order?.buyer?.buyer_name || order?.data?.buyer?.buyer_name || 'N/A' }}</span>
              </div>
              <div class="text-gray-500">{{ order?.buyer?.buyer_number || order?.data?.buyer?.buyer_number }}</div>
            </dd>
          </div>
          
          <!-- Admin -->
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Admin
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div>{{ order?.admin?.username || order?.data?.admin?.username || 'admin' }}</div>
              <div class="text-gray-500">{{ order?.admin?.role || order?.data?.admin?.role || 'system_admin' }}</div>
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
                  <span class="font-medium">Date:</span> {{ formatDateLong(order?.order_date || order?.data?.order_date) }}
                </div>
                <div>
                  <span class="font-medium">Payment Method:</span> {{ formatPaymentMethod(order?.payment_method || order?.data?.payment_method) }}
                </div>
                <div>
                  <span class="font-medium">Total Amount:</span> ${{ formatCurrency(order?.total_amount || order?.data?.total_amount || 0) }}
                </div>
              </div>
            </dd>
          </div>
          
          <!-- Shipping status -->
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Shipping status
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <span :class="[
                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                getStatusClass(order?.shipping_status || order?.data?.shipping_status)
              ]">
                {{ formatStatus(order?.shipping_status || order?.data?.shipping_status) }}
              </span>
            </dd>
          </div>
          
          <!-- Tracking number -->
          <div v-if="order?.tracking_number || order?.data?.tracking_number" class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Tracking number
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ order?.tracking_number || order?.data?.tracking_number }}
            </dd>
          </div>
          
          <!-- Estimated delivery -->
          <div v-if="order?.estimated_delivery || order?.data?.estimated_delivery" class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">
              Estimated delivery
            </dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ formatDateLong(order?.estimated_delivery || order?.data?.estimated_delivery) }}
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Order Components -->
    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <h3 class="text-lg font-medium text-gray-900">Order Components</h3>
      </div>
      <div class="border-t border-gray-200">
        <Table v-if="items && items.length > 0">
          <TableHead>
            <TableRow>
              <TableHeading>Component</TableHeading>
              <TableHeading>Category</TableHeading>
              <TableHeading>Supplier</TableHeading>
              <TableHeading>Quantity</TableHeading>
              <TableHeading>Unit Price</TableHeading>
              <TableHeading>Total</TableHeading>
            </TableRow>
          </TableHead>
          <tbody class="bg-white divide-y divide-gray-200">
            <TableRow v-for="item in items" :key="item.id">
              <TableCell>
                <div>
                  <div class="font-medium">{{ getComponentName(item) }}</div>
                  <div class="text-gray-500">{{ getComponentRef(item) }}</div>
                </div>
              </TableCell>
              <TableCell>{{ getCategory(item) }}</TableCell>
              <TableCell>{{ getSupplier(item) }}</TableCell>
              <TableCell>{{ item.quantity || 0 }}</TableCell>
              <TableCell>${{ formatCurrency(getPrice(item)) }}</TableCell>
              <TableCell>${{ formatCurrency(getPrice(item) * (item.quantity || 0)) }}</TableCell>
            </TableRow>
            <TableRow>
              <TableCell :colspan="5" class="text-right font-medium">Total Amount:</TableCell>
              <TableCell class="font-bold">${{ formatCurrency(order?.total_amount || order?.data?.total_amount || 0) }}</TableCell>
            </TableRow>
          </tbody>
        </Table>
        <div v-else class="text-center py-12">
          <p class="text-gray-500">No items found in this order.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: ['auth', 'admin'],
  title: 'View Order',
  key: (route: any) => `order-view-${route.params.id}`
})

const { api } = useApi()
const route = useRoute()
const router = useRouter()

// Debug: Log view page route
if (typeof window !== 'undefined') {
  console.log('👁️ VIEW PAGE ROUTE:', {
    path: route.path,
    params: route.params,
    name: route.name,
    fullPath: route.fullPath
  })
}

const orderId = route.params.id as string

// Fetch order details
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: orderData, pending } = await useLazyAsyncData(`order-${orderId}`, async () => {
  const response = await api(`/orders/${orderId}`, { method: 'GET' })
  return response
})

const order = computed(() => {
  const data = orderData.value
  // Handle different response formats
  // API might return: { data: {...} } or direct object
  if (data?.data) {
    return data.data
  }
  return data || null
})

// Extract items from order (selectedComponents)
const items = computed(() => {
  const orderObj = order.value
  if (!orderObj) return []
  
  // Check for selectedComponents or selected_components
  const components = orderObj.selectedComponents || orderObj.selected_components || []
  return Array.isArray(components) ? components : []
})

const getStatusClass = (status: string) => {
  if (!status) return 'bg-gray-100 text-gray-800'
  const statusLower = status.toLowerCase()
  const classes: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    shipped: 'bg-blue-100 text-blue-800',
    arrived_hub: 'bg-purple-100 text-purple-800',
    out_for_delivery: 'bg-indigo-100 text-indigo-800',
    completed: 'bg-green-100 text-green-800',
    canceled: 'bg-red-100 text-red-800',
    processing: 'bg-blue-100 text-blue-800'
  }
  return classes[statusLower] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status: string) => {
  if (!status) return 'N/A'
  return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const formatDateLong = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatPaymentMethod = (method: string) => {
  if (!method) return 'N/A'
  return method.charAt(0).toUpperCase() + method.slice(1)
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const getComponentName = (item: any) => {
  return item?.hardware_component?.component_name || 
         item?.hardwareComponent?.component_name || 
         item?.component_name || 
         'N/A'
}

const getComponentRef = (item: any) => {
  return item?.hardware_component?.component_reference_number || 
         item?.hardwareComponent?.component_reference_number || 
         item?.component_reference_number || 
         'N/A'
}

const getCategory = (item: any) => {
  return item?.hardware_component?.category?.category_name || 
         item?.hardwareComponent?.category?.category_name || 
         item?.category?.category_name || 
         'N/A'
}

const getSupplier = (item: any) => {
  return item?.hardware_component?.supplier?.supplier_name || 
         item?.hardwareComponent?.supplier?.supplier_name || 
         item?.supplier?.supplier_name || 
         'N/A'
}

const getPrice = (item: any) => {
  return item?.hardware_component?.retail_price || 
         item?.hardwareComponent?.retail_price || 
         item?.retail_price || 
         0
}

const clearMessage = () => {
  router.replace({ query: { ...route.query, success: undefined, error: undefined } })
}
</script>

