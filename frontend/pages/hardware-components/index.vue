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
        <i class="fas fa-microchip mr-3 text-purple-500"></i>
        Hardware Components
      </h2>
      <button 
        @click="goToCreate"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center"
        :disabled="navigating"
      >
        <i class="fas fa-plus mr-2"></i>Add New Component
      </button>
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
            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
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

    <div class="bg-white rounded shadow overflow-hidden">
      <Table>
        <TableHead>
          <TableHeading>Reference #</TableHeading>
          <TableHeading>Name</TableHeading>
          <TableHeading>Category</TableHeading>
          <TableHeading>Supplier</TableHeading>
          <TableHeading>Price</TableHeading>
          <TableHeading>Status</TableHeading>
          <TableHeading>Actions</TableHeading>
        </TableHead>

      <tbody class="bg-white divide-y divide-gray-200">
        <TableRow 
          v-for="(component, index) in components" 
          :key="component.id"
          :even="index % 2 === 0"
        >
          <TableCell>{{ component.component_reference_number }}</TableCell>
          <TableCell>
            <div class="text-sm">
              <div class="font-medium text-gray-900">{{ component.component_name }}</div>
              <div class="text-gray-500">{{ component.brand }} - {{ component.model }}</div>
            </div>
          </TableCell>
          <TableCell>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
              {{ component.category?.category_name || 'N/A' }}
            </span>
          </TableCell>
          <TableCell>{{ component.supplier?.supplier_name || 'N/A' }}</TableCell>
          <TableCell>
            <div class="text-sm">
              <div class="font-medium text-gray-900">${{ formatCurrency(component.retail_price) }}</div>
              <div class="text-gray-500">${{ formatCurrency(component.seller_price || 0) }} (seller)</div>
            </div>
          </TableCell>
          <TableCell>
            <span :class="[
              'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
              (component.selected_components_count || component.selectedComponents?.length || 0) > 0 
                ? 'bg-green-100 text-green-800' 
                : 'bg-yellow-100 text-yellow-800'
            ]">
              {{ (component.selected_components_count || component.selectedComponents?.length || 0) > 0 ? 'In Orders' : 'Available' }}
            </span>
          </TableCell>
          <TableCell>
            <div class="flex items-center space-x-3">
              <NuxtLink 
                :to="`/hardware-components/${component.id}`" 
                class="text-blue-600 hover:text-blue-900"
              >
                <i class="fas fa-eye"></i>
              </NuxtLink>
              <NuxtLink 
                :to="`/hardware-components/${component.id}/edit`" 
                class="text-yellow-600 hover:text-yellow-900"
                title="Edit"
              >
                <i class="fas fa-edit"></i>
              </NuxtLink>
              <button 
                :id="`delete-component-${component.id}`"
                :name="`delete-component-${component.id}`"
                @click.prevent="() => showDeleteConfirm(component)"
                type="button"
                class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="deleting"
                title="Delete this component"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </TableCell>
        </TableRow>
      </tbody>
      </Table>
    </div>

    <!-- Empty State -->
    <div v-if="!pending && components.length === 0" class="text-center py-8">
      <div class="flex flex-col items-center justify-center space-y-2">
        <i class="fas fa-microchip text-gray-400 text-5xl"></i>
        <p class="text-gray-500 text-lg">No hardware components found.</p>
        <NuxtLink to="/hardware-components/create" class="text-blue-500 hover:text-blue-700">
          Add your first component
        </NuxtLink>
      </div>
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

// Local filter state (not synced to route until button is clicked)
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

const deleting = ref(false)
const navigating = ref(false)
const showConfirmModal = ref(false)
const componentToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

// Fetch categories and suppliers
const { data: categoriesData } = await useAsyncData('categories', () =>
  api('/categories', { method: 'GET' })
)

const { data: suppliersData } = await useAsyncData('suppliers', () =>
  api('/suppliers', { method: 'GET' })
)

const categories = computed(() => {
  const data = categoriesData.value
  if (Array.isArray(data)) return data
  if (data?.data && Array.isArray(data.data)) return data.data
  return []
})

const suppliers = computed(() => {
  const data = suppliersData.value
  if (Array.isArray(data)) return data
  if (data?.data && Array.isArray(data.data)) return data.data
  return []
})

// Fetch components with filters (only uses activeFilters, not local filters)
const { data: componentsData, pending, refresh } = await useLazyAsyncData(
  () => `hardware-components-${route.query.page || 1}-${activeFilters.category || ''}-${activeFilters.supplier || ''}-${activeFilters.search || ''}`,
  () => {
    const params = new URLSearchParams()
    if (activeFilters.category) params.append('category', activeFilters.category)
    if (activeFilters.supplier) params.append('supplier', activeFilters.supplier)
    if (activeFilters.search) params.append('search', activeFilters.search)
    if (route.query.page) params.append('page', route.query.page as string)
    
    return api(`/hardware-components?${params.toString()}`, { method: 'GET' })
  }
)

const components = computed(() => {
  const data = componentsData.value
  if (!data) return []
  
  // Handle Laravel paginator response (data at root level)
  if (data.data && Array.isArray(data.data)) {
    return data.data
  }
  
  // Handle direct array response
  if (Array.isArray(data)) {
    return data
  }
  
  return []
})

const pagination = computed(() => {
  const data = componentsData.value
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
  activeFilters.category = filters.category
  activeFilters.supplier = filters.supplier
  activeFilters.search = filters.search
  
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

const goToCreate = async () => {
  if (navigating.value) return
  
  navigating.value = true
  showLoading('Loading...', 'Navigating to create component page')
  
  try {
    await router.push('/hardware-components/create')
  } catch (error: any) {
    console.error('Navigation error:', error)
    hideLoading()
  } finally {
    navigating.value = false
    // Hide loading after a short delay to ensure navigation completes
    setTimeout(() => {
      hideLoading()
    }, 300)
  }
}

const clearFilters = async () => {
  showLoading('Clearing filters...', 'Please wait')
  
  // Clear local filters
  filters.category = ''
  filters.supplier = ''
  filters.search = ''
  
  // Clear active filters
  activeFilters.category = ''
  activeFilters.supplier = ''
  activeFilters.search = ''
  
  router.push({ path: '/hardware-components', query: {} })
  
  // Wait for route to update, then refresh data
  await nextTick()
  await refresh()
  hideLoading()
}

const showDeleteConfirm = (component: any) => {
  const orderCount = component.selected_components_count || component.selectedComponents?.length || 0
  componentToDelete.value = component
  confirmMessage.value = orderCount > 0
    ? `Are you sure you want to delete this component? This component is currently in ${orderCount} order(s).`
    : 'Are you sure you want to delete this component?'
  confirmDetails.value = `Component: ${component.component_name} — ${component.component_reference_number}${orderCount > 0 ? ` (in ${orderCount} order(s))` : ''}`
  showConfirmModal.value = true
}

const cancelDelete = () => {
  showConfirmModal.value = false
  componentToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const executeDelete = async () => {
  if (!componentToDelete.value) return

  showConfirmModal.value = false
  deleting.value = true

  try {
    await withLoading(
      async () => {
        await api(`/hardware-components/${componentToDelete.value.id}`, {
          method: 'DELETE'
        })
        
        // Show success message and refresh list
        flashMessage.value = `Component "${componentToDelete.value.component_name}" deleted successfully.`
        flashMessageType.value = 'success'
        await refresh()
        
        setTimeout(clearFlashMessage, 5000)
      },
      'Deleting component...',
      'Please wait'
    )
  } catch (error: any) {
    console.error('Failed to delete component:', error)
    const errorMsg = error.data?.message || 'Failed to delete component. Please try again.'
    flashMessage.value = errorMsg
    flashMessageType.value = 'error'
    setTimeout(clearFlashMessage, 5000)
  } finally {
    deleting.value = false
    componentToDelete.value = null
  }
}

const clearFlashMessage = () => {
  flashMessage.value = ''
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

