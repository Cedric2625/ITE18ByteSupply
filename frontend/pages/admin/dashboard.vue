<template>
  <div>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>
          Orders
        </div>
        <div class="text-2xl font-semibold">{{ stats?.orders || 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-users mr-2 text-green-500"></i>
          Buyers
        </div>
        <div class="text-2xl font-semibold">{{ stats?.buyers || 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-microchip mr-2 text-purple-500"></i>
          Components
        </div>
        <div class="text-2xl font-semibold">{{ stats?.components || 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-building mr-2 text-orange-500"></i>
          Suppliers
        </div>
        <div class="text-2xl font-semibold">{{ stats?.suppliers || 0 }}</div>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-folder mr-2 text-indigo-500"></i>
          Categories
        </div>
        <div class="text-2xl font-semibold">{{ stats?.categories || 0 }}</div>
      </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded shadow">
      <div class="p-4 border-b font-semibold flex items-center">
        <i class="fas fa-list-alt mr-2 text-gray-600"></i>
        Recent Orders
      </div>
      <div class="p-4">
        <Table>
          <TableHead>
            <TableHeading>Ref #</TableHeading>
            <TableHeading>Buyer</TableHeading>
            <TableHeading>Status</TableHeading>
            <TableHeading>Total</TableHeading>
            <TableHeading>Placed</TableHeading>
            <TableHeading></TableHeading>
          </TableHead>
          <tbody>
            <TableRow 
              v-for="(order, index) in recentOrders" 
              :key="order.id"
              :even="index % 2 === 0"
            >
              <TableCell>{{ order.order_reference_number }}</TableCell>
              <TableCell>{{ order.buyer?.buyer_name }}</TableCell>
              <TableCell>{{ capitalize(order.shipping_status) }}</TableCell>
              <TableCell>${{ formatCurrency(order.total_amount) }}</TableCell>
              <TableCell>{{ formatRelativeTime(order.created_at) }}</TableCell>
              <TableCell>
                <NuxtLink 
                  :to="`/admin/orders/${order.id}`" 
                  class="text-indigo-600 hover:text-indigo-800"
                >
                  View
                </NuxtLink>
              </TableCell>
            </TableRow>
          </tbody>
        </Table>
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
definePageMeta({
  middleware: ['auth', 'admin']
})

const { api } = useApi()
const { isLoading, loadingMessage, loadingSubMessage } = useLoading()

// Fetch dashboard data
const { data: dashboardData, pending } = await useLazyAsyncData('admin-dashboard', async () => {
  // Fetch stats from orders/statistics endpoint
  const statsRes = await api<{ total_orders?: number; buyers_count?: number }>('/orders/statistics', { method: 'GET' })
  
  // Fetch recent orders
  const ordersRes = await api<{ data?: any[] }>('/orders?per_page=10', { method: 'GET' })
  
  // Fetch buyers count
  const buyersCountRes = await api<{ total_buyers?: number; count?: number }>('/buyers/count', { method: 'GET' })
  
  // Fetch hardware components (paginated, Laravel returns total at root level)
  const componentsRes = await api<{ data?: any[]; total?: number; meta?: { total?: number } }>('/hardware-components?per_page=1', { method: 'GET' })
  
  // Fetch suppliers (paginated response)
  const suppliersRes = await api<{ data?: any[]; total?: number; meta?: { total?: number } }>('/suppliers?per_page=1', { method: 'GET' })
  
  // Fetch categories (paginated response)
  const categoriesRes = await api<{ data?: any[]; total?: number; meta?: { total?: number } }>('/categories?per_page=1', { method: 'GET' })
  
  return {
    stats: {
      orders: statsRes.total_orders || 0,
      buyers: buyersCountRes.total_buyers || buyersCountRes.count || (Array.isArray(buyersCountRes) ? buyersCountRes.length : 0),
      components: componentsRes.total || componentsRes.meta?.total || (Array.isArray(componentsRes) ? componentsRes.length : 0),
      suppliers: suppliersRes.total || suppliersRes.meta?.total || (Array.isArray(suppliersRes) ? suppliersRes.length : 0),
      categories: categoriesRes.total || categoriesRes.meta?.total || (Array.isArray(categoriesRes) ? categoriesRes.length : 0)
    },
    recentOrders: ordersRes.data || []
  }
})

const stats = computed(() => dashboardData.value?.stats)
const recentOrders = computed(() => dashboardData.value?.recentOrders || [])

// Helper functions
const capitalize = (str: string) => {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatRelativeTime = (date: string) => {
  const now = new Date()
  const then = new Date(date)
  const diffInSeconds = Math.floor((now.getTime() - then.getTime()) / 1000)
  
  if (diffInSeconds < 60) return 'Just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`
  
  return then.toLocaleDateString()
}
</script>

