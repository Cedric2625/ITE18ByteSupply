<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label for="buyer_name" value="Buyer Name" />
        <Input
          id="buyer_name"
          v-model="form.buyer_name"
          type="text"
          required
          autofocus
        />
        <p v-if="errors.buyer_name" class="mt-1 text-sm text-red-600">
          {{ errors.buyer_name }}
        </p>
      </div>

      <div>
        <Label for="buyer_number" value="Buyer Number" />
        <Input
          id="buyer_number"
          v-model="form.buyer_number"
          type="text"
          required
        />
        <p v-if="errors.buyer_number" class="mt-1 text-sm text-red-600">
          {{ errors.buyer_number }}
        </p>
      </div>

      <div>
        <Label for="email" value="Email Address" />
        <Input
          id="email"
          v-model="form.email"
          type="email"
          required
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email }}
        </p>
      </div>

      <div>
        <Label for="address" value="Address" />
        <textarea
          id="address"
          v-model="form.address"
          rows="3"
          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required
        ></textarea>
        <p v-if="errors.address" class="mt-1 text-sm text-red-600">
          {{ errors.address }}
        </p>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          to="/admin/buyers" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Buyer' }}
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
  buyer_name: '',
  buyer_number: '',
  email: '',
  address: ''
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/buyers', {
      method: 'POST',
      body: form
    })
    
    await router.push('/admin/buyers')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create buyer' }
    }
  } finally {
    loading.value = false
  }
}
</script>

