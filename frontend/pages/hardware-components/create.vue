<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label for="component_reference_number" value="Reference Number" />
        <Input
          id="component_reference_number"
          v-model="form.component_reference_number"
          type="text"
          required
          autofocus
        />
        <p v-if="errors.component_reference_number" class="mt-1 text-sm text-red-600">
          {{ errors.component_reference_number }}
        </p>
      </div>

      <div>
        <Label for="component_name" value="Component Name" />
        <Input
          id="component_name"
          v-model="form.component_name"
          type="text"
          required
        />
        <p v-if="errors.component_name" class="mt-1 text-sm text-red-600">
          {{ errors.component_name }}
        </p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label for="brand" value="Brand" />
          <Input
            id="brand"
            v-model="form.brand"
            type="text"
            required
          />
          <p v-if="errors.brand" class="mt-1 text-sm text-red-600">
            {{ errors.brand }}
          </p>
        </div>
        <div>
          <Label for="model" value="Model" />
          <Input
            id="model"
            v-model="form.model"
            type="text"
            required
          />
          <p v-if="errors.model" class="mt-1 text-sm text-red-600">
            {{ errors.model }}
          </p>
        </div>
      </div>

      <div>
        <Label for="specifications" value="Specifications" />
        <textarea
          id="specifications"
          v-model="form.specifications"
          rows="3"
          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required
        ></textarea>
        <p v-if="errors.specifications" class="mt-1 text-sm text-red-600">
          {{ errors.specifications }}
        </p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label for="retail_price" value="Retail Price" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <Input
              id="retail_price"
              v-model.number="form.retail_price"
              type="number"
              step="0.01"
              min="0"
              class="pl-7"
              required
            />
          </div>
          <p v-if="errors.retail_price" class="mt-1 text-sm text-red-600">
            {{ errors.retail_price }}
          </p>
        </div>
        <div>
          <Label for="seller_price" value="Seller Price" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <Input
              id="seller_price"
              v-model.number="form.seller_price"
              type="number"
              step="0.01"
              min="0"
              class="pl-7"
              required
            />
          </div>
          <p v-if="errors.seller_price" class="mt-1 text-sm text-red-600">
            {{ errors.seller_price }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label for="category_id" value="Category" />
          <select
            id="category_id"
            v-model.number="form.category_id"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          >
            <option value="">Select Category</option>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
            >
              {{ category.category_name }}
            </option>
          </select>
          <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">
            {{ errors.category_id }}
          </p>
        </div>
        <div>
          <Label for="supplier_id" value="Supplier" />
          <select
            id="supplier_id"
            v-model.number="form.supplier_id"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          >
            <option value="">Select Supplier</option>
            <option
              v-for="supplier in suppliers"
              :key="supplier.id"
              :value="supplier.id"
            >
              {{ supplier.supplier_name }}
            </option>
          </select>
          <p v-if="errors.supplier_id" class="mt-1 text-sm text-red-600">
            {{ errors.supplier_id }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <Label for="date_created" value="Creation Date" />
          <Input
            id="date_created"
            v-model="form.date_created"
            type="date"
            required
          />
          <p v-if="errors.date_created" class="mt-1 text-sm text-red-600">
            {{ errors.date_created }}
          </p>
        </div>
        <div>
          <Label for="date_order" value="Order Date" />
          <Input
            id="date_order"
            v-model="form.date_order"
            type="date"
          />
          <p v-if="errors.date_order" class="mt-1 text-sm text-red-600">
            {{ errors.date_order }}
          </p>
        </div>
        <div>
          <Label for="date_arrive" value="Arrival Date" />
          <Input
            id="date_arrive"
            v-model="form.date_arrive"
            type="date"
          />
          <p v-if="errors.date_arrive" class="mt-1 text-sm text-red-600">
            {{ errors.date_arrive }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          to="/hardware-components" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Component' }}
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

// Fetch categories and suppliers
const { data: categoriesData } = await useAsyncData('categories', () =>
  api('/categories', { method: 'GET' })
)
const { data: suppliersData } = await useAsyncData('suppliers', () =>
  api('/suppliers', { method: 'GET' })
)

const categories = computed(() => categoriesData.value?.data || [])
const suppliers = computed(() => suppliersData.value?.data || [])

const form = reactive({
  component_reference_number: '',
  component_name: '',
  brand: '',
  model: '',
  specifications: '',
  retail_price: 0,
  seller_price: 0,
  category_id: null as number | null,
  supplier_id: null as number | null,
  date_created: new Date().toISOString().split('T')[0],
  date_order: '',
  date_arrive: ''
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/hardware-components', {
      method: 'POST',
      body: form
    })
    
    await router.push('/hardware-components')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create component' }
    }
  } finally {
    loading.value = false
  }
}
</script>

