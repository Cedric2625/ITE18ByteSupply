<template>
  <div>
    <h2 class="text-2xl font-semibold mb-6 flex items-center">
      <i class="fas fa-history mr-3 text-green-500"></i>
      Order History
    </h2>

    <p class="text-gray-600 mb-6">View your completed orders and their details.</p>

    <!-- Orders List -->
    <div v-if="orders.length > 0" class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow"
      >
        <!-- Order Header -->
        <div class="flex items-center justify-between mb-4 pb-4 border-b">
          <div>
            <h3 class="text-lg font-semibold text-gray-900">
              Order #{{ order.order_reference_number }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ formatDate(order.order_date || order.created_at || '') }}
            </p>
          </div>
          <div class="text-right">
            <span :class="[
              'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full',
              getStatusClass(order.shipping_status)
            ]">
              {{ formatStatus(order.shipping_status) }}
            </span>
            <p class="text-xl font-bold text-gray-900 mt-2">
              ${{ formatCurrency(order.total_amount) }}
            </p>
          </div>
        </div>

        <!-- Order Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <span class="text-sm text-gray-500">Payment Method:</span>
            <p class="font-medium">{{ formatPaymentMethod(order.payment_method) }}</p>
          </div>
          <div v-if="order.tracking_number">
            <span class="text-sm text-gray-500">Tracking Number:</span>
            <p class="font-medium">{{ order.tracking_number }}</p>
          </div>
          <div v-if="order.estimated_delivery">
            <span class="text-sm text-gray-500">Estimated Delivery:</span>
            <p class="font-medium">{{ formatDate(order.estimated_delivery) }}</p>
          </div>
        </div>

        <!-- Order Items Summary -->
        <div class="mt-4 pt-4 border-t">
          <h4 class="text-sm font-semibold text-gray-700 mb-2">Order Items:</h4>
          <div class="space-y-2">
            <div
              v-for="item in getOrderItems(order)"
              :key="item.id"
              class="flex justify-between items-center text-sm"
            >
              <span class="text-gray-700">
                {{ item.hardware_component?.component_name || item.hardwareComponent?.component_name || 'N/A' }}
                <span class="text-gray-500">x{{ item.quantity }}</span>
              </span>
              <span class="font-medium text-gray-900">
                ${{ formatCurrency((item.hardware_component?.retail_price || item.hardwareComponent?.retail_price || 0) * item.quantity) }}
              </span>
            </div>
          </div>
        </div>

        <!-- View Details Link -->
        <div class="mt-4 pt-4 border-t">
          <NuxtLink
            :to="`/shop/orders/${order.id}`"
            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium"
          >
            <i class="fas fa-eye mr-2"></i>
            View Full Details
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!pending && orders.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
      <i class="fas fa-inbox text-4xl text-gray-400 mb-4"></i>
      <p class="text-gray-500 text-lg">No completed orders yet.</p>
      <p class="text-gray-400 text-sm mt-2">Your completed orders will appear here.</p>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.last_page && pagination.last_page > 1" class="mt-6">
      <div class="flex justify-center space-x-2">
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-4 py-2 rounded',
            pagination.current_page === page 
              ? 'bg-green-500 text-white' 
              : 'bg-white text-gray-700 hover:bg-gray-100'
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <!-- Loading Modal -->
    <LoadingModal
      :show="isLoading || pending"
      :message="loadingMessage"
      :subMessage="loadingSubMessage"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
// @ts-ignore - useApi is auto-imported by Nuxt
import { useApi } from '~/composables/useApi'

interface HardwareComponent {
  id: number
  component_name: string
  brand?: string
  retail_price: number
  [key: string]: any
}

interface OrderItem {
  id: number
  quantity: number
  hardware_component?: HardwareComponent
  hardwareComponent?: HardwareComponent
  [key: string]: any
}

interface Order {
  id: number
  order_reference_number: string
  order_date?: string
  created_at?: string
  total_amount: number
  shipping_status: string
  payment_method?: string
  tracking_number?: string
  estimated_delivery?: string
  selectedComponents?: OrderItem[]
  selected_components?: OrderItem[]
  [key: string]: any
}

interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

interface OrdersResponse {
  data?: Order[]
  meta?: PaginationMeta
}

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: 'auth'
})

const { api } = useApi()
const route = useRoute()
const router = useRouter()
const { isLoading, loadingMessage, loadingSubMessage, showLoading, hideLoading } = useLoading()

// Fetch completed orders only
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: ordersData, pending, refresh } = await useLazyAsyncData<OrdersResponse>(
  () => `order-history-${route.query.page || 1}`,
  () => {
    const params = new URLSearchParams()
    if (route.query.page) params.append('page', route.query.page as string)
    // Only fetch completed orders
    params.append('status', 'completed')
    
    return api<OrdersResponse>(`/shop/orders?${params.toString()}`, { method: 'GET' })
  }
)

const orders = computed<Order[]>(() => {
  const data = ordersData.value
  if (!data) return []
  // Handle Laravel paginator format (data at root level)
  if (data.current_page !== undefined) {
    return (data.data || []) as Order[]
  }
  // Handle custom API response format
  if (Array.isArray(data)) return data as Order[]
  return (data?.data || []) as Order[]
})

const pagination = computed<PaginationMeta | undefined>(() => {
  const data = ordersData.value
  if (!data) return undefined
  // Handle Laravel paginator format (meta at root level)
  if (data.current_page !== undefined) {
    return {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total
    }
  }
  // Handle custom API response format
  return data?.meta
})

const goToPage = async (page: number) => {
  showLoading('Loading page...', `Page ${page}`)
  router.push({
    query: {
      ...route.query,
      page
    }
  })
  await refresh()
  hideLoading()
}

const getOrderItems = (order: Order): OrderItem[] => {
  return order.selectedComponents || order.selected_components || []
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    completed: 'bg-green-100 text-green-800',
    shipped: 'bg-blue-100 text-blue-800',
    out_for_delivery: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    arrived_hub: 'bg-yellow-100 text-yellow-800',
    canceled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status: string) => {
  return status.replace(/_/g, ' ').split(' ').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatPaymentMethod = (method: string | undefined) => {
  if (!method) return 'N/A'
  return method.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDate = (date: string) => {
  if (!date) return ''
  const d = new Date(date)
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
}
</script>

