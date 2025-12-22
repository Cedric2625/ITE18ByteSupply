<template>
  <div>
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

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold flex items-center">
        <i class="fas fa-shopping-cart mr-3 text-blue-500"></i>
        Order List
      </h2>
      <NuxtLink 
        to="/admin/orders/create" 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>Create New Order
      </NuxtLink>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="mb-4 flex items-center text-gray-700">
        <i class="fas fa-filter mr-2"></i>
        <span class="font-medium">Filters</span>
      </div>
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label for="buyer" class="block text-sm font-medium text-gray-700">Buyer</label>
          <select 
            id="buyer" 
            v-model="filters.buyer"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="">All Buyers</option>
            <option v-for="buyer in buyers" :key="buyer.id" :value="buyer.id">
              {{ buyer.buyer_name }}
            </option>
          </select>
        </div>
        <div>
          <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
          <select 
            id="status" 
            v-model="filters.status"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="shipped">Shipped</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div>
          <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
          <div class="grid grid-cols-2 gap-2">
            <input 
              type="date" 
              id="start_date"
              v-model="filters.start_date"
              class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              placeholder="From"
            />
            <input 
              type="date" 
              id="end_date"
              v-model="filters.end_date"
              class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              placeholder="To"
            />
          </div>
        </div>
        <div class="flex items-end space-x-2">
          <button 
            type="submit" 
            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <i class="fas fa-search mr-2"></i>Filter
          </button>
          <button 
            type="button"
            @click="clearFilters"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <i class="fas fa-times mr-2"></i>Clear
          </button>
        </div>
      </form>
    </div>

    <Table>
      <TableHead>
        <TableHeading>Order #</TableHeading>
        <TableHeading>Buyer</TableHeading>
        <TableHeading>Date</TableHeading>
        <TableHeading>Total</TableHeading>
        <TableHeading>Status</TableHeading>
        <TableHeading>Actions</TableHeading>
      </TableHead>

      <tbody class="bg-white divide-y divide-gray-200">
        <TableRow 
          v-for="(order, index) in orders" 
          :key="order.id"
          :even="index % 2 === 0"
        >
          <TableCell>{{ order.order_reference_number }}</TableCell>
          <TableCell>
            <div class="text-sm">
              <div class="font-medium text-gray-900">{{ order.buyer?.buyer_name || 'N/A' }}</div>
              <div class="text-gray-500">{{ order.buyer?.buyer_number || '' }}</div>
            </div>
          </TableCell>
          <TableCell>{{ formatDate(order.order_date || order.created_at) }}</TableCell>
          <TableCell>${{ formatCurrency(order.total_amount) }}</TableCell>
          <TableCell>
            <span :class="[
              'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
              getStatusClass(order.shipping_status)
            ]">
              {{ capitalize(order.shipping_status) }}
            </span>
          </TableCell>
          <TableCell>
            <div class="flex items-center space-x-3">
              <NuxtLink 
                :to="`/admin/orders/${order.id}`" 
                class="text-blue-600 hover:text-blue-900 transition-colors"
                title="View"
              >
                <i class="fas fa-eye"></i>
              </NuxtLink>
              <NuxtLink 
                :to="`/admin/orders/${order.id}/edit`" 
                class="text-yellow-600 hover:text-yellow-900 transition-colors cursor-pointer inline-block"
                title="Edit"
                style="pointer-events: auto !important; z-index: 10; position: relative; min-width: 20px; min-height: 20px; padding: 4px;"
                @click="(e) => { 
                  console.log('🔵 Edit button clicked for order:', order.id);
                  console.log('🔵 Navigating to:', `/admin/orders/${order.id}/edit`);
                }"
              >
                <i class="fas fa-edit"></i>
              </NuxtLink>
              <button 
                @click="() => showDeleteConfirm(order)"
                class="text-red-600 hover:text-red-900 transition-colors"
                title="Delete"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </TableCell>
        </TableRow>
      </tbody>
    </Table>

    <!-- Empty State -->
    <div v-if="!pending && orders.length === 0" class="text-center py-8">
      <div class="flex flex-col items-center justify-center space-y-2">
        <i class="fas fa-shopping-cart text-gray-400 text-5xl"></i>
        <p class="text-gray-500 text-lg">No orders found.</p>
        <NuxtLink 
          to="/admin/orders/create" 
          class="text-blue-500 hover:text-blue-700"
        >
          Create your first order
        </NuxtLink>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmModal
      :show="showConfirmModal"
      :message="confirmMessage"
      :details="confirmDetails"
      @confirm="handleDelete"
      @cancel="cancelDelete"
    />

    <!-- Status Legend -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-3">
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-yellow-200"></span>
        <span class="text-sm text-gray-600">Pending / Arrived Hub</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-blue-200"></span>
        <span class="text-sm text-gray-600">Processing / Shipped / Out for delivery</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-green-200"></span>
        <span class="text-sm text-gray-600">Completed / Delivered</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-red-200"></span>
        <span class="text-sm text-gray-600">Cancelled</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-gray-200"></span>
        <span class="text-sm text-gray-600">Other</span>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination
      v-if="pagination && pagination.last_page > 1"
      :currentPage="pagination.current_page"
      :totalPages="pagination.last_page"
      @page-change="goToPage"
    />

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
const route = useRoute()
const router = useRouter()
const { isLoading, loadingMessage, loadingSubMessage, showLoading, hideLoading, withLoading } = useLoading()

const showConfirmModal = ref(false)
const orderToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')

// Local filter state (not synced to route until button is clicked)
const filters = reactive({
  buyer: (route.query.buyer as string) || '',
  status: (route.query.status as string) || '',
  start_date: (route.query.start_date as string) || '',
  end_date: (route.query.end_date as string) || ''
})

// Active filters used for API calls (synced with route.query)
const activeFilters = reactive({
  buyer: (route.query.buyer as string) || '',
  status: (route.query.status as string) || '',
  start_date: (route.query.start_date as string) || '',
  end_date: (route.query.end_date as string) || ''
})

// Fetch buyers for filter dropdown
const { data: buyersData } = await useLazyAsyncData('buyers-list', () =>
  api('/buyers', { method: 'GET' })
)

const buyers = computed(() => {
  const data = buyersData.value
  if (Array.isArray(data)) return data
  return data?.data || []
})

// Fetch orders with filters (only uses activeFilters, not local filters)
const { data: ordersData, pending, refresh } = await useLazyAsyncData(
  () => {
    // Create a reactive key based on route query and activeFilters
    const page = route.query.page || '1'
    const buyer = route.query.buyer || activeFilters.buyer || ''
    const status = route.query.status || activeFilters.status || ''
    const startDate = route.query.start_date || activeFilters.start_date || ''
    const endDate = route.query.end_date || activeFilters.end_date || ''
    return `admin-orders-${page}-${buyer}-${status}-${startDate}-${endDate}`
  },
  async () => {
    const params = new URLSearchParams()
    if (activeFilters.buyer) params.append('buyer', activeFilters.buyer)
    if (activeFilters.status) params.append('status', activeFilters.status)
    if (activeFilters.start_date) params.append('start_date', activeFilters.start_date)
    if (activeFilters.end_date) params.append('end_date', activeFilters.end_date)
    if (route.query.page) params.append('page', route.query.page as string)
    // Add per_page for pagination
    params.append('per_page', '15')
    
    const response = await api(`/orders?${params.toString()}`, { method: 'GET' })
    
    // Debug: Log response structure
    if (process.dev) {
      console.log('📦 Orders API Response:', {
        hasData: !!response?.data,
        hasMeta: !!response?.meta,
        isArray: Array.isArray(response),
        firstOrder: response?.data?.[0] || response?.[0],
        responseKeys: Object.keys(response || {})
      })
    }
    
    return response
  }
)

const orders = computed(() => {
  const data = ordersData.value
  if (!data) return []
  
  // Handle Laravel paginator response (data at root level)
  if (data.data && Array.isArray(data.data)) {
    return data.data
  }
  
  // Handle custom structure with data property
  if (data?.data && Array.isArray(data.data)) {
    return data.data
  }
  
  // Handle direct array response
  if (Array.isArray(data)) {
    return data
  }
  
  // Fallback to empty array
  return []
})

const pagination = computed(() => {
  const data = ordersData.value
  if (!data) return null
  
  // Handle Laravel paginator response (pagination at root level)
  if (data.current_page !== undefined) {
    return {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total
    }
  }
  
  // Handle meta property if present
  if (data.meta) {
    return data.meta
  }
  
  return null
})

const applyFilters = async () => {
  showLoading('Applying filters...', 'Please wait')
  
  // Update active filters from local filters
  activeFilters.buyer = filters.buyer
  activeFilters.status = filters.status
  activeFilters.start_date = filters.start_date
  activeFilters.end_date = filters.end_date
  
  // Update route query
  router.push({
    query: {
      ...filters,
      page: 1
    }
  })
  
  // Wait for route to update, then refresh data
  await nextTick()
  await refresh()
  hideLoading()
}

const goToPage = async (page: number) => {
  showLoading('Loading page...', `Page ${page}`)
  // Update route query
  await router.push({
    query: {
      ...route.query,
      page: page.toString()
    }
  })
  // Force refresh to ensure data is fetched with new page parameter
  await refresh()
  hideLoading()
}

const clearFilters = async () => {
  showLoading('Clearing filters...', 'Please wait')
  
  // Clear local filters
  filters.buyer = ''
  filters.status = ''
  filters.start_date = ''
  filters.end_date = ''
  
  // Clear active filters
  activeFilters.buyer = ''
  activeFilters.status = ''
  activeFilters.start_date = ''
  activeFilters.end_date = ''
  
  // Navigate to orders index without any query parameters (same as backend)
  router.push({ path: '/admin/orders', query: {} })
  
  // Wait for route to update, then refresh data
  await nextTick()
  await refresh()
  hideLoading()
}

const clearMessage = () => {
  router.replace({ query: { ...route.query, success: undefined, error: undefined } })
}

const showDeleteConfirm = (order: any) => {
  orderToDelete.value = order
  confirmMessage.value = 'Are you sure you want to delete this order?'
  confirmDetails.value = `Order: ${order.order_reference_number}`
  showConfirmModal.value = true
}

const cancelDelete = () => {
  showConfirmModal.value = false
  orderToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const handleDelete = async () => {
  if (!orderToDelete.value) return

  showConfirmModal.value = false

  try {
    await withLoading(
      async () => {
        await api(`/orders/${orderToDelete.value.id}`, { method: 'DELETE' })
        // Refresh the list after successful deletion (same as backend redirects to index)
        await refresh()
      },
      'Deleting order...',
      'Please wait'
    )
    // Show success message via URL query (will be displayed by Alert component in layout)
    await router.push({
      path: '/admin/orders',
      query: { success: `Order "${orderToDelete.value.order_reference_number}" deleted successfully.` }
    })
  } catch (error: any) {
    // Show error message via URL query
    await router.push({
      path: '/admin/orders',
      query: { error: error.data?.message || 'Failed to delete order. Please try again.' }
    })
  } finally {
    orderToDelete.value = null
  }
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    delivered: 'bg-green-100 text-green-800',
    shipped: 'bg-blue-100 text-blue-800',
    processing: 'bg-yellow-100 text-yellow-800',
    cancelled: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const capitalize = (str: string) => {
  return str.charAt(0).toUpperCase() + str.slice(1)
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
  return d.toLocaleDateString()
}
</script>

