<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label for="category_name_create" value="Category Name" />
        <Input
          id="category_name_create"
          name="category_name_create"
          v-model="form.category_name"
          type="text"
          autocomplete="organization"
          required
          autofocus
        />
        <p v-if="errors.category_name" class="mt-1 text-sm text-red-600">
          {{ errors.category_name }}
        </p>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          to="/categories" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Category' }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin']
})

const { api } = useApi()
const router = useRouter()

const form = reactive({
  category_name: ''
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/categories', {
      method: 'POST',
      body: form
    })
    
    await router.push('/categories')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create category' }
    }
  } finally {
    loading.value = false
  }
}
</script>

