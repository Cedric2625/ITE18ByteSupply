<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label for="supplier_name" value="Supplier Name" />
        <Input
          id="supplier_name"
          v-model="form.supplier_name"
          type="text"
          required
          autofocus
        />
        <p v-if="errors.supplier_name" class="mt-1 text-sm text-red-600">
          {{ errors.supplier_name }}
        </p>
      </div>

      <div>
        <Label for="contact_person" value="Contact Person" />
        <Input
          id="contact_person"
          v-model="form.contact_person"
          type="text"
          required
        />
        <p v-if="errors.contact_person" class="mt-1 text-sm text-red-600">
          {{ errors.contact_person }}
        </p>
      </div>

      <div>
        <Label for="phone_number" value="Phone Number" />
        <Input
          id="phone_number"
          v-model="form.phone_number"
          type="tel"
          required
          placeholder="+1 (555) 123-4567"
        />
        <p v-if="errors.phone_number" class="mt-1 text-sm text-red-600">
          {{ errors.phone_number }}
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

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          to="/admin/suppliers" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Supplier' }}
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
  supplier_name: '',
  contact_person: '',
  phone_number: '',
  email: ''
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/suppliers', {
      method: 'POST',
      body: form
    })
    
    await router.push('/admin/suppliers')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create supplier' }
    }
  } finally {
    loading.value = false
  }
}
</script>

