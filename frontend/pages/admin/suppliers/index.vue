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
        <i class="fas fa-building mr-3 text-orange-500"></i>
        Supplier List
      </h2>
      <NuxtLink to="/admin/suppliers/create" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        <i class="fas fa-plus mr-2"></i> Add New Supplier
      </NuxtLink>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <Table>
        <TableHead>
          <TableHeading>Supplier Name</TableHeading>
          <TableHeading>Contact Person</TableHeading>
          <TableHeading>Contact Info</TableHeading>
          <TableHeading>Components</TableHeading>
          <TableHeading>Actions</TableHeading>
        </TableHead>
        <tbody>
          <TableRow 
            v-for="(supplier, index) in suppliers" 
            :key="supplier.id"
            :even="index % 2 === 0"
          >
            <TableCell class="font-medium">{{ supplier.supplier_name }}</TableCell>
            <TableCell>{{ supplier.contact_person }}</TableCell>
            <TableCell>
              <div class="text-sm">
                <a :href="`mailto:${supplier.email}`" class="text-blue-600 hover:text-blue-900">
                  {{ supplier.email }}
                </a>
                <br>
                <span class="text-gray-500">{{ supplier.phone_number }}</span>
              </div>
            </TableCell>
            <TableCell>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ supplier.hardware_components_count || 0 }} components
              </span>
            </TableCell>
            <TableCell>
              <div class="flex items-center space-x-3">
                <NuxtLink 
                  :to="`/admin/suppliers/${supplier.id}`" 
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                  title="View"
                >
                  <i class="fas fa-eye"></i>
                </NuxtLink>
                <NuxtLink 
                  :to="`/admin/suppliers/${supplier.id}/edit`" 
                  class="text-yellow-600 hover:text-yellow-900 transition-colors"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </NuxtLink>
                <button 
                  :id="`delete-supplier-${supplier.id}`"
                  :name="`delete-supplier-${supplier.id}`"
                  @click.prevent="() => showDeleteConfirm(supplier)" 
                  type="button"
                  class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  :disabled="deleting"
                  title="Delete this supplier"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="!pending && suppliers.length === 0">
            <TableCell colspan="5" class="text-center py-8">
              <div class="flex flex-col items-center justify-center space-y-2">
                <i class="fas fa-building text-gray-400 text-5xl"></i>
                <p class="text-gray-500 text-lg">No suppliers found.</p>
                <NuxtLink to="/admin/suppliers/create" class="text-blue-500 hover:text-blue-700">
                  Add your first supplier
                </NuxtLink>
              </div>
            </TableCell>
          </TableRow>
        </tbody>
      </Table>
    </div>

    <!-- Pagination -->
    <Pagination
      v-if="pagination && pagination.last_page > 1"
      :currentPage="pagination.current_page"
      :totalPages="pagination.last_page"
      @page-change="goToPage"
    />

    <!-- Confirmation Modal -->
    <ConfirmModal
      :show="showConfirmModal"
      :message="confirmMessage"
      :details="confirmDetails"
      @confirm="executeDelete"
      @cancel="cancelDelete"
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

// Fetch suppliers with pagination
const { data: suppliersData, pending, refresh } = await useLazyAsyncData(
  () => `suppliers-${route.query.page || 1}`,
  async () => {
    const params = new URLSearchParams()
    if (route.query.page) params.append('page', route.query.page as string)
    
    const response = await api(`/suppliers?${params.toString()}`, { method: 'GET' })
    
    // Handle paginated response (Laravel paginator format)
    if (response.data && Array.isArray(response.data)) {
      return response
    }
    
    // Fallback for non-paginated response
    return { data: response.data || response || [], meta: null }
  }
)

const suppliers = computed(() => {
  const data = suppliersData.value
  if (!data) return []
  
  // Handle Laravel paginator response
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
  const data = suppliersData.value
  if (data?.meta) {
    return data.meta
  }
  // If no meta, check if response has pagination at root level
  if (data?.current_page !== undefined) {
    return {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total
    }
  }
  return null
})

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

const deleting = ref(false)
const showConfirmModal = ref(false)
const supplierToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

const showDeleteConfirm = (supplier: any) => {
  const componentCount = supplier.hardware_components_count || 0
  supplierToDelete.value = supplier
  confirmMessage.value = componentCount > 0
    ? `Are you sure you want to delete this supplier? This will also delete ${componentCount} associated hardware component(s).`
    : 'Are you sure you want to delete this supplier?'
  confirmDetails.value = `Supplier: ${supplier.supplier_name}${componentCount > 0 ? ` (${componentCount} component(s) will be deleted)` : ''}`
  showConfirmModal.value = true
}

const cancelDelete = () => {
  showConfirmModal.value = false
  supplierToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const executeDelete = async () => {
  if (!supplierToDelete.value) return

  showConfirmModal.value = false
  deleting.value = true

  try {
    await withLoading(
      async () => {
        await api(`/suppliers/${supplierToDelete.value.id}`, {
          method: 'DELETE'
        })
        
        flashMessage.value = `Supplier "${supplierToDelete.value.supplier_name}" deleted successfully.`
        flashMessageType.value = 'success'
        await refresh()
        
        setTimeout(clearFlashMessage, 5000)
      },
      'Deleting supplier...',
      'Please wait'
    )
  } catch (error: any) {
    console.error('Failed to delete supplier:', error)
    const errorMsg = error.data?.message || 'Failed to delete supplier. Please try again.'
    flashMessage.value = errorMsg
    flashMessageType.value = 'error'
    setTimeout(clearFlashMessage, 5000)
  } finally {
    deleting.value = false
    supplierToDelete.value = null
  }
}

const clearFlashMessage = () => {
  flashMessage.value = ''
}
</script>

