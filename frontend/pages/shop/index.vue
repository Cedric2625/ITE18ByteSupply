<template>
  <div>
    <div class="mb-6">
      <h1 class="text-3xl font-bold flex items-center">
        <i class="fas fa-store mr-3 text-indigo-500"></i>
        Shop
      </h1>
    </div>
    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
      <div class="mb-4 flex items-center text-gray-700">
        <i class="fas fa-filter mr-2"></i>
        <span class="font-medium">Filters</span>
      </div>
      <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <Label for="category" value="Category" />
          <select 
            id="category" 
            v-model="filters.category"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
          >
            <option value="">All</option>
            <option 
              v-for="category in categories" 
              :key="category.id" 
              :value="category.id"
            >
              {{ category.category_name }}
            </option>
          </select>
        </div>
        <div>
          <Label for="supplier" value="Supplier" />
          <select 
            id="supplier" 
            v-model="filters.supplier"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300"
          >
            <option value="">All</option>
            <option 
              v-for="supplier in suppliers" 
              :key="supplier.id" 
              :value="supplier.id"
            >
              {{ supplier.supplier_name }}
            </option>
          </select>
        </div>
        <div>
          <Label for="search" value="Search" />
          <div class="relative mt-1">
            <Input 
              id="search" 
              v-model="filters.search" 
              type="text"
              class="block w-full pl-10"
            />
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>
        <div class="flex items-end gap-2">
          <Button type="submit">
            <i class="fas fa-filter mr-2"></i>Filter
          </Button>
          <Button type="button" @click="clearFilters" variant="orange">
            <i class="fas fa-times mr-2"></i>Clear
          </Button>
        </div>
      </form>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        v-for="component in components"
        :key="component.id"
        class="bg-white rounded-lg shadow p-4 flex flex-col"
      >
        <div class="mb-2">
          <h3 class="text-lg font-semibold">{{ component.component_name }}</h3>
          <p class="text-sm text-gray-500">
            {{ component.brand }} - {{ component.model }}
          </p>
        </div>
        <p class="text-sm text-gray-700 mb-3">{{ component.specifications }}</p>
        <div class="mt-auto flex items-center justify-between">
          <div>
            <div class="text-xl font-bold">
              ${{ formatCurrency(component.retail_price) }}
            </div>
            <div class="text-xs text-gray-500">
              Stock: {{ component.stock_quantity }}
            </div>
          </div>
          <form @submit.prevent="addToCart(component)" class="flex items-center space-x-2">
            <Input 
              v-model.number="cartQuantities[component.id]"
              type="number" 
              :min="1" 
              :max="component.stock_quantity" 
              class="w-20"
            />
            <Button type="submit">Add to cart</Button>
          </form>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!pending && components.length === 0" class="text-center py-12">
      <p class="text-gray-500">No components found.</p>
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

    <!-- Success Modal for Add to Cart -->
    <SuccessModal
      :show="showSuccessModal"
      :message="successModalMessage"
      title="Success!"
      @close="showSuccessModal = false"
    />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  // Shop browse is public - no auth required
})

const { api } = useApi()
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { isLoading, loadingMessage, loadingSubMessage, showLoading, hideLoading, withLoading } = useLoading()

const { flashError } = useFlash()

// Success modal state
const showSuccessModal = ref(false)
const successModalMessage = ref('')

// Handle Google OAuth callback - check for token in URL
onMounted(() => {
  if (process.client) {
    const token = route.query.token as string
    const success = route.query.success as string
    const error = route.query.error as string
    
    if (token) {
      // Store token from Google OAuth
      authStore.setToken(token)
      // Fetch user info
      useAuth().fetchUser().then(() => {
        // Remove token from URL
        router.replace({ query: { ...route.query, token: undefined, success: undefined } })
      })
    }
    
    if (success) {
      // Show success message
      flashSuccess(decodeURIComponent(success), 5000)
      // Remove success from URL after a delay
      setTimeout(() => {
        router.replace({ query: { ...route.query, success: undefined } })
      }, 3000)
    }
    
    if (error) {
      // Show error message
      flashError(decodeURIComponent(error), 5000)
      // Redirect to login if error
      router.replace('/auth/login?' + new URLSearchParams({ error: error as string }).toString())
    }
  }
})

// Local filters for form inputs (not automatically applied)
const filters = reactive({
  category: (route.query.category as string) || '',
  supplier: (route.query.supplier as string) || '',
  search: (route.query.search as string) || ''
})

// Active filters used for API calls (synced with route.query)
const activeFilters = reactive({
  category: (route.query.category as string) || '',
  supplier: (route.query.supplier as string) || '',
  search: (route.query.search as string) || ''
})

const cartQuantities = ref<Record<number, number>>({})

// Fetch shop data (categories, suppliers, components) from shop API
const { data: shopData, pending, refresh } = await useLazyAsyncData(
  () => `shop-data-${route.query.page || 1}-${activeFilters.category || ''}-${activeFilters.supplier || ''}-${activeFilters.search || ''}`,
  () => {
    const params = new URLSearchParams()
    if (activeFilters.category) params.append('category', activeFilters.category)
    if (activeFilters.supplier) params.append('supplier', activeFilters.supplier)
    if (activeFilters.search) params.append('search', activeFilters.search)
    if (route.query.page) params.append('page', route.query.page as string)
    
    return api<{ 
      data?: any[]
      categories?: any[]
      suppliers?: any[]
      meta?: { current_page?: number; last_page?: number; per_page?: number; total?: number }
    }>(`/shop?${params.toString()}`, { method: 'GET' })
  }
)

const categories = computed(() => shopData.value?.categories || [])
const suppliers = computed(() => shopData.value?.suppliers || [])
const components = computed(() => shopData.value?.data || [])
const pagination = computed(() => shopData.value?.meta || {})

const applyFilters = async () => {
  showLoading('Applying filters...', 'Please wait')
  
  // Sync active filters with local filters
  activeFilters.category = filters.category
  activeFilters.supplier = filters.supplier
  activeFilters.search = filters.search
  
  // Update route query
  await router.push({
    query: {
      category: activeFilters.category || undefined,
      supplier: activeFilters.supplier || undefined,
      search: activeFilters.search || undefined,
      page: 1
    }
  })
  
  // Wait for route to update, then refresh data
  await nextTick()
  await refresh()
  hideLoading()
}

const clearFilters = async () => {
  showLoading('Clearing filters...', 'Please wait')
  
  // Reset local filters
  filters.category = ''
  filters.supplier = ''
  filters.search = ''
  
  // Reset active filters
  activeFilters.category = ''
  activeFilters.supplier = ''
  activeFilters.search = ''
  
  // Update route query
  await router.push({
    query: {
      page: route.query.page || undefined
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

const addToCart = async (component: any) => {
  const quantity = cartQuantities.value[component.id] || 1
  
  // Check if user is authenticated
  const { isAuthenticated } = useAuth()
  if (!isAuthenticated.value) {
    await router.push('/auth/login?redirect=/shop')
    return
  }
  
  try {
    await withLoading(
      async () => {
        const response = await api<{ status?: string; message?: string; component?: any }>('/shop/add', {
          method: 'POST',
          body: {
            component_id: component.id,
            quantity
          }
        })
        
        if (response.status === 'success') {
          // Add to local cart storage
          if (process.client) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]')
            const existingIndex = cart.findIndex((item: any) => item.id === component.id)
            
            if (existingIndex >= 0) {
              cart[existingIndex].quantity += quantity
            } else {
              cart.push({
                id: component.id,
                name: component.component_name,
                price: component.retail_price,
                quantity
              })
            }
            
            localStorage.setItem('cart', JSON.stringify(cart))
            // Trigger cart count update in layout
            window.dispatchEvent(new CustomEvent('cart-updated'))
          }
          
          // Reset quantity input
          cartQuantities.value[component.id] = 1
          
          // Show success modal
          successModalMessage.value = `Successfully added "${component.component_name}" to cart!`
          showSuccessModal.value = true
        }
      },
      'Adding to cart...',
      'Please wait'
    )
  } catch (error: any) {
    console.error('Failed to add to cart:', error)
    flashError(error.data?.message || 'Failed to add to cart', 5000)
  }
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

