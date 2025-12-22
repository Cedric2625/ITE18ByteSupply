<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label :for="`supplier_name_${route.params.id}`" value="Supplier Name" />
        <Input
          :id="`supplier_name_${route.params.id}`"
          :name="`supplier_name_${route.params.id}`"
          v-model="form.supplier_name"
          type="text"
          autocomplete="organization"
          required
        />
        <p v-if="errors.supplier_name" class="mt-1 text-sm text-red-600">
          {{ errors.supplier_name }}
        </p>
      </div>

      <div>
        <Label :for="`contact_person_${route.params.id}`" value="Contact Person" />
        <Input
          :id="`contact_person_${route.params.id}`"
          :name="`contact_person_${route.params.id}`"
          v-model="form.contact_person"
          type="text"
          autocomplete="name"
          required
        />
        <p v-if="errors.contact_person" class="mt-1 text-sm text-red-600">
          {{ errors.contact_person }}
        </p>
      </div>

      <div>
        <Label :for="`phone_number_${route.params.id}`" value="Phone Number" />
        <Input
          :id="`phone_number_${route.params.id}`"
          :name="`phone_number_${route.params.id}`"
          v-model="form.phone_number"
          type="tel"
          autocomplete="tel"
          required
          placeholder="+1 (555) 123-4567"
        />
        <p v-if="errors.phone_number" class="mt-1 text-sm text-red-600">
          {{ errors.phone_number }}
        </p>
      </div>

      <div>
        <Label :for="`email_${route.params.id}`" value="Email Address" />
        <Input
          :id="`email_${route.params.id}`"
          :name="`email_${route.params.id}`"
          v-model="form.email"
          type="email"
          autocomplete="email"
          required
        />
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email }}
        </p>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          :to="`/admin/suppliers/${route.params.id}`" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Updating...' : 'Update Supplier' }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin']
})

const route = useRoute()
const { api } = useApi()
const router = useRouter()

const { data: supplierData } = await useLazyAsyncData(`supplier-${route.params.id}`, () =>
  api(`/suppliers/${route.params.id}`, { method: 'GET' })
)

const supplier = computed(() => supplierData.value?.data || supplierData.value)

const form = reactive({
  supplier_name: '',
  contact_person: '',
  phone_number: '',
  email: ''
})

// Populate form when supplier data loads
watch(supplier, (newSupplier) => {
  if (newSupplier) {
    form.supplier_name = newSupplier.supplier_name
    form.contact_person = newSupplier.contact_person
    form.phone_number = newSupplier.phone_number
    form.email = newSupplier.email
  }
}, { immediate: true })

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    const response = await api(`/suppliers/${route.params.id}`, {
      method: 'PATCH',
      body: form
    })
    
    console.log('Update response:', response)
    
    // Redirect to show page with success message
    await router.push({
      path: `/admin/suppliers/${route.params.id}`,
      query: { success: 'Supplier updated successfully' }
    })
  } catch (error: any) {
    console.error('Failed to update supplier:', error)
    
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
      errors.value = { 
        general: error.data?.message || error.message || 'Failed to update supplier. Please try again.' 
      }
    }
  } finally {
    loading.value = false
  }
}
</script>

