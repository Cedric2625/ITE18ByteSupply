<template>
  <div>
    <!-- Success/Error Messages -->
    <div v-if="flashMessage" :class="[
      'mb-4 px-4 py-3 rounded relative max-w-7xl mx-auto',
      flashMessageType === 'success' 
        ? 'bg-green-100 border border-green-400 text-green-700' 
        : 'bg-red-100 border border-red-400 text-red-700'
    ]" role="alert">
      <span class="block sm:inline">{{ flashMessage }}</span>
      <button @click="clearFlashMessage" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold flex items-center">
        <i class="fas fa-users mr-3 text-green-500"></i>
        Buyer List
      </h2>
    </div>

    <!-- Search Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="mb-4 flex items-center text-gray-700">
        <i class="fas fa-filter mr-2"></i>
        <span class="font-medium">Search</span>
      </div>
      <form @submit.prevent="applySearch" class="flex items-end gap-4">
        <div class="flex-1">
          <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Buyers</label>
          <div class="relative">
            <Input
              id="search"
              v-model="searchQuery"
              type="text"
              placeholder="Search buyers by name, email, or number"
              class="w-full pl-10"
            />
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
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
            @click="clearSearch"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
          >
            <i class="fas fa-times mr-2"></i>Clear
          </button>
        </div>
      </form>
    </div>

    <div v-if="stats" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-users mr-2 text-blue-500"></i>
          Total Buyers
        </div>
        <div class="text-2xl font-semibold">{{ stats.total || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-check-circle mr-2 text-green-500"></i>
          Active (with orders)
        </div>
        <div class="text-2xl font-semibold text-green-600">{{ stats.active || 0 }}</div>
      </div>
      <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center text-sm text-gray-500 mb-2">
          <i class="fas fa-user-slash mr-2 text-gray-500"></i>
          Inactive
        </div>
        <div class="text-2xl font-semibold text-gray-700">{{ stats.inactive || 0 }}</div>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <Table>
        <TableHead>
          <TableHeading>Name</TableHeading>
          <TableHeading>Number</TableHeading>
          <TableHeading>Email</TableHeading>
          <TableHeading>Orders</TableHeading>
          <TableHeading>Actions</TableHeading>
        </TableHead>
        <tbody>
          <TableRow 
            v-for="(buyer, index) in buyers" 
            :key="buyer.id"
            :even="index % 2 === 0"
          >
            <TableCell>{{ buyer.buyer_name }}</TableCell>
            <TableCell>{{ buyer.buyer_number }}</TableCell>
            <TableCell>
              <a :href="`mailto:${buyer.email}`" class="text-blue-600 hover:text-blue-900">
                {{ buyer.email }}
              </a>
            </TableCell>
            <TableCell>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ buyer.orders_count || 0 }} orders
              </span>
            </TableCell>
            <TableCell>
              <div class="flex items-center space-x-3">
                <NuxtLink 
                  :to="`/admin/buyers/${buyer.id}`" 
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                  title="View"
                >
                  <i class="fas fa-eye"></i>
                </NuxtLink>
                <button 
                  @click="() => showDeleteConfirm(buyer)" 
                  class="text-red-600 hover:text-red-900 transition-colors"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="!pending && buyers.length === 0">
            <TableCell colspan="5" class="text-center py-8">
              <div class="flex flex-col items-center justify-center space-y-2">
                <i class="fas fa-users text-gray-400 text-5xl"></i>
                <p class="text-gray-500 text-lg">No buyers found.</p>
                <NuxtLink to="/admin/buyers/create" class="text-blue-500 hover:text-blue-700">
                  Add your first buyer
                </NuxtLink>
              </div>
            </TableCell>
          </TableRow>
        </tbody>
      </Table>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmModal
      :show="showConfirmModal"
      :message="confirmMessage"
      :details="confirmDetails"
      @confirm="executeDelete"
      @cancel="cancelDelete"
    />

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

// Local search query (not synced to route until button is clicked)
const searchQuery = ref((route.query.search as string) || '')

// Active search query used for API calls (synced with route.query)
const activeSearchQuery = ref((route.query.search as string) || '')

const { data: buyersData, pending, refresh } = await useLazyAsyncData(
  () => `buyers-${route.query.page || 1}-${activeSearchQuery.value || ''}`,
  async () => {
    const params = new URLSearchParams()
    if (activeSearchQuery.value) params.append('search', activeSearchQuery.value)
    if (route.query.page) params.append('page', route.query.page as string)
    params.append('per_page', '10')
    
    const response = await api<{ 
      data?: Array<any>; 
      stats?: { total: number; active: number; inactive: number };
      current_page?: number;
      last_page?: number;
      per_page?: number;
      total?: number;
    }>(`/buyers?${params.toString()}`, { method: 'GET' })
    
    return {
      data: response.data || [],
      stats: response.stats || { total: 0, active: 0, inactive: 0 },
      pagination: {
        current_page: response.current_page || 1,
        last_page: response.last_page || 1,
        per_page: response.per_page || 10,
        total: response.total || 0
      }
    }
  }
)

const buyers = computed(() => buyersData.value?.data || [])
const stats = computed(() => buyersData.value?.stats || { total: 0, active: 0, inactive: 0 })
const pagination = computed(() => buyersData.value?.pagination || null)

const applySearch = async () => {
  showLoading('Searching buyers...', 'Please wait')
  
  // Update active search from local search
  activeSearchQuery.value = searchQuery.value
  
  router.push({ 
    query: { 
      search: searchQuery.value,
      page: 1
    } 
  })
  
  await nextTick()
  await refresh()
  hideLoading()
}

const clearSearch = async () => {
  showLoading('Clearing search...', 'Please wait')
  
  // Clear local search
  searchQuery.value = ''
  
  // Clear active search
  activeSearchQuery.value = ''
  
  router.push({ 
    path: '/admin/buyers',
    query: { page: route.query.page || 1 }
  })
  
  await nextTick()
  await refresh()
  hideLoading()
}

const goToPage = async (page: number) => {
  showLoading('Loading page...', `Page ${page}`)
  await router.push({
    query: {
      ...route.query,
      page: page.toString()
    }
  })
  await nextTick()
  await refresh()
  hideLoading()
}

const deleting = ref(false)
const showConfirmModal = ref(false)
const buyerToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

const showDeleteConfirm = (buyer: any) => {
  buyerToDelete.value = buyer
  confirmMessage.value = 'Are you sure you want to delete this buyer?'
  confirmDetails.value = `Buyer: ${buyer.buyer_name} — ${buyer.email}`
  showConfirmModal.value = true
}

const cancelDelete = () => {
  showConfirmModal.value = false
  buyerToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const executeDelete = async () => {
  if (!buyerToDelete.value) return

  showConfirmModal.value = false
  deleting.value = true

  const buyer = buyerToDelete.value
  const orderCount = buyer.orders_count || 0
  let forceDelete = false

  try {
    // If buyer has orders, try force delete
    if (orderCount > 0) {
      forceDelete = true
      confirmMessage.value = `Buyer "${buyer.buyer_name}" has ${orderCount} order(s). This will delete the buyer and ALL associated orders. Are you sure you want to continue?`
      confirmDetails.value = `Buyer: ${buyer.buyer_name} — ${buyer.email}`
      showConfirmModal.value = true
      return
    }

    await withLoading(
      async () => {
        await api(`/buyers/${buyer.id}`, {
          method: 'DELETE'
        })
        
        flashMessage.value = `Buyer "${buyer.buyer_name}" deleted successfully.`
        flashMessageType.value = 'success'
        await refresh()
        
        setTimeout(clearFlashMessage, 5000)
      },
      'Deleting buyer...',
      'Please wait'
    )
  } catch (error: any) {
    const errorMsg = error.data?.message || error.message || 'Failed to delete buyer'
    
    // If error suggests force delete, show modal for confirmation
    if (errorMsg.includes('force delete') || errorMsg.includes('associated orders')) {
      if (!forceDelete) {
        forceDelete = true
        confirmMessage.value = errorMsg + '\n\nDo you want to delete the buyer and all associated orders?'
        confirmDetails.value = `Buyer: ${buyer.buyer_name} — ${buyer.email}`
        showConfirmModal.value = true
        deleting.value = false
        return
      }
      
      // Retry with force delete
      try {
        await withLoading(
          async () => {
            await api(`/buyers/${buyer.id}?force=true`, { method: 'DELETE' })
            flashMessage.value = `Buyer "${buyer.buyer_name}" and all associated orders deleted successfully.`
            flashMessageType.value = 'success'
            await refresh()
            setTimeout(clearFlashMessage, 5000)
          },
          'Deleting buyer and orders...',
          'This may take a moment'
        )
      } catch (forceError: any) {
        flashMessage.value = forceError.data?.message || 'Failed to force delete buyer'
        flashMessageType.value = 'error'
        setTimeout(clearFlashMessage, 5000)
      }
    } else {
      flashMessage.value = errorMsg
      flashMessageType.value = 'error'
      setTimeout(clearFlashMessage, 5000)
    }
  } finally {
    deleting.value = false
    if (!showConfirmModal.value) {
      buyerToDelete.value = null
    }
  }
}

const clearFlashMessage = () => {
  flashMessage.value = ''
}
</script>

