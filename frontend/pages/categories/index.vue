<template>
  <div>
    <!-- Success/Error Messages -->
    <div v-if="flashMessage" :class="[
      'mb-4 px-4 py-3 rounded relative',
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
        <i class="fas fa-folder mr-3 text-indigo-500"></i>
        Category List
      </h2>
      <NuxtLink 
        to="/categories/create" 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition-colors"
      >
        <i class="fas fa-plus mr-2"></i>Add New Category
      </NuxtLink>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <Table>
        <TableHead>
          <TableHeading>Category Name</TableHeading>
          <TableHeading>Components</TableHeading>
          <TableHeading>Created At</TableHeading>
          <TableHeading>Actions</TableHeading>
        </TableHead>

        <tbody class="bg-white divide-y divide-gray-200">
          <TableRow 
            v-for="(category, index) in categories" 
            :key="category.id"
            :even="index % 2 === 0"
          >
            <TableCell class="font-medium">{{ category.category_name }}</TableCell>
            <TableCell>
              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ category.hardware_components_count || 0 }} components
              </span>
            </TableCell>
            <TableCell>{{ formatDate(category.created_at) }}</TableCell>
            <TableCell>
              <div class="flex items-center space-x-3">
                <NuxtLink 
                  :to="`/categories/${category.id}`" 
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                  title="View"
                >
                  <i class="fas fa-eye"></i>
                </NuxtLink>
                <NuxtLink 
                  :to="`/categories/${category.id}/edit`" 
                  class="text-yellow-600 hover:text-yellow-900 transition-colors"
                  title="Edit"
                  @click="() => console.log('Edit clicked for category:', category.id)"
                >
                  <i class="fas fa-edit"></i>
                </NuxtLink>
                <button 
                  :id="`delete-category-${category.id}`"
                  :name="`delete-category-${category.id}`"
                  @click.prevent="handleDeleteClick(category, $event)"
                  type="button"
                  class="text-red-600 hover:text-red-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  :disabled="deleting"
                  title="Delete this category"
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
    <div v-if="!pending && categories.length === 0" class="text-center py-12">
      <div class="flex flex-col items-center justify-center space-y-2">
        <i class="fas fa-folder text-gray-400 text-5xl"></i>
        <p class="text-gray-500 text-lg">No categories found.</p>
        <NuxtLink to="/categories/create" class="text-blue-500 hover:text-blue-700">
          Add your first category
        </NuxtLink>
      </div>
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

const deleting = ref(false)
const showConfirmModal = ref(false)
const categoryToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

// Fetch categories with pagination
const { data: categoriesData, pending, refresh } = await useLazyAsyncData(
  () => `categories-${route.query.page || 1}`,
  async () => {
    const params = new URLSearchParams()
    if (route.query.page) params.append('page', route.query.page as string)
    
    const response = await api(`/categories?${params.toString()}`, { method: 'GET' })
    
    // Handle paginated response (Laravel paginator format)
    if (response.data && Array.isArray(response.data)) {
      return response
    }
    
    // Fallback for non-paginated response
    return { data: response.data || response || [], meta: null }
  }
)

const categories = computed(() => {
  const data = categoriesData.value
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
  const data = categoriesData.value
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

const handleDeleteClick = (category: any, event?: Event) => {
  console.log('Delete button clicked for category:', category.id, category.category_name)
  if (event) {
    event.preventDefault()
    event.stopPropagation()
  }
  showDeleteConfirm(category)
}

const showDeleteConfirm = (category: any) => {
  console.log('showDeleteConfirm called for:', category.category_name)
  
  const componentCount = category.hardware_components_count || 0
  categoryToDelete.value = category
  confirmMessage.value = componentCount > 0 
    ? `Are you sure you want to delete this category? This will also delete ${componentCount} associated hardware component(s).`
    : `Are you sure you want to delete this category?`
  confirmDetails.value = `Category: ${category.category_name}${componentCount > 0 ? ` (${componentCount} component(s) will be deleted)` : ''}`
  showConfirmModal.value = true
  
  console.log('Modal state set:', {
    showConfirmModal: showConfirmModal.value,
    category: category.category_name,
    confirmMessage: confirmMessage.value
  })
}

const cancelDelete = () => {
  showConfirmModal.value = false
  categoryToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const executeDelete = async () => {
  if (!categoryToDelete.value) return

  showConfirmModal.value = false
  deleting.value = true

  try {
    await withLoading(
      async () => {
        const response = await api(`/categories/${categoryToDelete.value.id}`, {
          method: 'DELETE'
        })
        
        console.log('Delete response:', response)
        
        // Show success message and refresh list
        const categoryName = categoryToDelete.value.category_name
        flashMessage.value = `Category "${categoryName}" deleted successfully.`
        flashMessageType.value = 'success'
        
        // Refresh the categories list
        await refresh()
        
        // Clear flash message after 5 seconds
        setTimeout(clearFlashMessage, 5000)
      },
      'Deleting category...',
      'Please wait'
    )
  } catch (error: any) {
    console.error('Failed to delete category:', error)
    
    // Extract error message from various possible formats
    let errorMsg = 'Failed to delete category. Please try again.'
    
    if (error.data?.message) {
      errorMsg = error.data.message
    } else if (error.message) {
      errorMsg = error.message
    } else if (error.statusMessage) {
      errorMsg = error.statusMessage
    } else if (typeof error === 'string') {
      errorMsg = error
    }
    
    // Handle specific error cases
    if (error.status === 422 || error.statusCode === 422) {
      errorMsg = error.data?.message || 'Cannot delete category with associated components.'
    } else if (error.status === 401 || error.statusCode === 401) {
      errorMsg = 'Unauthorized. Please login again.'
    } else if (error.status === 404 || error.statusCode === 404) {
      errorMsg = 'Category not found.'
    }
    
    flashMessage.value = errorMsg
    flashMessageType.value = 'error'
    setTimeout(clearFlashMessage, 5000)
  } finally {
    deleting.value = false
    categoryToDelete.value = null
  }
}

const clearFlashMessage = () => {
  flashMessage.value = ''
}

const formatDate = (date: string) => {
  const d = new Date(date)
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`
}
</script>
