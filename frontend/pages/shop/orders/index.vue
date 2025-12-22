<template>
  <div>
    <h2 class="text-2xl font-semibold mb-6 flex items-center">
      <i class="fas fa-shopping-bag mr-3 text-indigo-500"></i>
      My Orders
    </h2>

    <Table>
      <TableHead>
        <TableHeading>Order #</TableHeading>
        <TableHeading>Date</TableHeading>
        <TableHeading>Total</TableHeading>
        <TableHeading>Status</TableHeading>
        <TableHeading></TableHeading>
      </TableHead>
      <tbody>
        <TableRow 
          v-for="(order, index) in orders" 
          :key="order.id"
          :even="index % 2 === 0"
        >
          <TableCell class="font-medium">{{ order.order_reference_number }}</TableCell>
          <TableCell>{{ formatDate(order.order_date || order.created_at || '') }}</TableCell>
          <TableCell>${{ formatCurrency(order.total_amount) }}</TableCell>
          <TableCell>
            <span :class="[
              'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
              getStatusClass(order.shipping_status)
            ]">
              {{ formatStatus(order.shipping_status) }}
            </span>
          </TableCell>
          <TableCell>
            <NuxtLink 
              :to="`/shop/orders/${order.id}`" 
              class="text-indigo-600 hover:text-indigo-800"
            >
              View
            </NuxtLink>
          </TableCell>
        </TableRow>
      </tbody>
    </Table>

    <!-- Empty State -->
    <div v-if="!pending && orders.length === 0" class="text-center py-12">
      <p class="text-gray-500">No orders yet.</p>
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
              ? 'bg-indigo-500 text-white' 
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

interface Order {
  id: number
  order_reference_number: string
  order_date?: string
  created_at?: string
  total_amount: number
  shipping_status: string
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

// Fetch customer orders (excluding completed)
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: ordersData, pending, refresh } = await useLazyAsyncData<OrdersResponse>(
  () => `customer-orders-${route.query.page || 1}`,
  () => {
    const params = new URLSearchParams()
    if (route.query.page) params.append('page', route.query.page as string)
    // Exclude completed orders - they should only appear in Order History
    params.append('exclude_completed', 'true')
    
    // Get orders for current buyer - use shop/orders endpoint
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

