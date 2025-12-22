<template>
  <div class="max-w-2xl mx-auto">
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

    <!-- Page Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-semibold text-gray-900">Edit Category</h2>
      <p class="mt-1 text-sm text-gray-500">Update the category information below.</p>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label :for="`category_name_${route.params.id}`" class="block text-sm font-medium text-gray-700">
              Category Name <span class="text-red-500">*</span>
            </label>
            <input
              :id="`category_name_${route.params.id}`"
              :name="`category_name_${route.params.id}`"
              v-model="form.category_name"
              type="text"
              autocomplete="organization"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              placeholder="Enter category name"
              required
              :disabled="loading || !category"
            />
            <p v-if="errors.category_name" class="mt-1 text-sm text-red-600">
              {{ errors.category_name }}
            </p>
            <p v-if="errors.general" class="mt-1 text-sm text-red-600">
              {{ errors.general }}
            </p>
          </div>

          <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
            <NuxtLink 
              :to="`/categories/${route.params.id}`" 
              class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
              Cancel
            </NuxtLink>

            <button
              type="submit"
              :disabled="loading || !category"
              class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
            >
              <span v-if="loading">Updating...</span>
              <span v-else>Update Category</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="!category && !errors.general" class="text-center py-8">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
      <p class="mt-2 text-sm text-gray-500">Loading category...</p>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin'],
  title: 'Edit Category'
})

const route = useRoute()
const { api } = useApi()
const router = useRouter()

// Fetch category data
const { data: categoryData, pending: categoryPending } = await useLazyAsyncData(`category-edit-${route.params.id}`, async () => {
  try {
    const response = await api(`/categories/${route.params.id}`, { method: 'GET' })
    console.log('Category data loaded:', response)
    return response
  } catch (error) {
    console.error('Failed to load category:', error)
    throw error
  }
})

const category = computed(() => {
  const data = categoryData.value
  if (!data) return null
  // Handle different response formats
  return data.data || data
})

const form = reactive({
  category_name: ''
})

// Populate form when category data loads
watch(category, (newCategory) => {
  if (newCategory) {
    form.category_name = newCategory.category_name
  }
}, { immediate: true })

const loading = ref(false)
const errors = ref<Record<string, string>>({})
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

// Watch for success message from query params
onMounted(() => {
  const route = useRoute()
  if (route.query.success) {
    flashMessage.value = route.query.success as string
    flashMessageType.value = 'success'
    setTimeout(clearFlashMessage, 5000)
  }
})

const clearFlashMessage = () => {
  flashMessage.value = ''
}

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}
  flashMessage.value = ''

  try {
    const response = await api(`/categories/${route.params.id}`, {
      method: 'PATCH',
      body: form
    })
    
    console.log('Update response:', response)
    
    // Show success message
    flashMessage.value = 'Category updated successfully!'
    flashMessageType.value = 'success'
    
    // Redirect to show page after a short delay
    setTimeout(async () => {
      await router.push({
        path: `/categories/${route.params.id}`,
        query: { success: 'Category updated successfully' }
      })
    }, 1500)
  } catch (error: any) {
    console.error('Failed to update category:', error)
    
    if (error.data?.errors) {
      // Handle validation errors
      const validationErrors = error.data.errors
      if (typeof validationErrors === 'object') {
        Object.keys(validationErrors).forEach(key => {
          errors.value[key] = Array.isArray(validationErrors[key]) 
            ? validationErrors[key][0] 
            : validationErrors[key]
        })
      }
    } else {
      const errorMsg = error.data?.message || error.message || 'Failed to update category. Please try again.'
      errors.value = { general: errorMsg }
      flashMessage.value = errorMsg
      flashMessageType.value = 'error'
      setTimeout(clearFlashMessage, 5000)
    }
  } finally {
    loading.value = false
  }
}
</script>

