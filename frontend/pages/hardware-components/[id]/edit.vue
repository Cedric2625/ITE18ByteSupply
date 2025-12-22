<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label :for="`component_reference_number_${route.params.id}`" value="Reference Number" />
        <Input
          :id="`component_reference_number_${route.params.id}`"
          :name="`component_reference_number_${route.params.id}`"
          v-model="form.component_reference_number"
          type="text"
          autocomplete="off"
          required
        />
        <p v-if="errors.component_reference_number" class="mt-1 text-sm text-red-600">
          {{ errors.component_reference_number }}
        </p>
      </div>

      <div>
        <Label :for="`component_name_${route.params.id}`" value="Component Name" />
        <Input
          :id="`component_name_${route.params.id}`"
          :name="`component_name_${route.params.id}`"
          v-model="form.component_name"
          type="text"
          autocomplete="off"
          required
        />
        <p v-if="errors.component_name" class="mt-1 text-sm text-red-600">
          {{ errors.component_name }}
        </p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label :for="`brand_${route.params.id}`" value="Brand" />
          <Input
            :id="`brand_${route.params.id}`"
            :name="`brand_${route.params.id}`"
            v-model="form.brand"
            type="text"
            autocomplete="organization"
            required
          />
          <p v-if="errors.brand" class="mt-1 text-sm text-red-600">
            {{ errors.brand }}
          </p>
        </div>
        <div>
          <Label :for="`model_${route.params.id}`" value="Model" />
          <Input
            :id="`model_${route.params.id}`"
            :name="`model_${route.params.id}`"
            v-model="form.model"
            type="text"
            autocomplete="off"
            required
          />
          <p v-if="errors.model" class="mt-1 text-sm text-red-600">
            {{ errors.model }}
          </p>
        </div>
      </div>

      <div>
        <Label :for="`specifications_${route.params.id}`" value="Specifications" />
        <textarea
          :id="`specifications_${route.params.id}`"
          :name="`specifications_${route.params.id}`"
          v-model="form.specifications"
          rows="3"
          autocomplete="off"
          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          required
        ></textarea>
        <p v-if="errors.specifications" class="mt-1 text-sm text-red-600">
          {{ errors.specifications }}
        </p>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label :for="`retail_price_${route.params.id}`" value="Retail Price" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <Input
              :id="`retail_price_${route.params.id}`"
              :name="`retail_price_${route.params.id}`"
              v-model.number="form.retail_price"
              type="number"
              step="0.01"
              min="0"
              autocomplete="off"
              class="pl-7"
              required
            />
          </div>
          <p v-if="errors.retail_price" class="mt-1 text-sm text-red-600">
            {{ errors.retail_price }}
          </p>
        </div>
        <div>
          <Label :for="`seller_price_${route.params.id}`" value="Seller Price" />
          <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <Input
              :id="`seller_price_${route.params.id}`"
              :name="`seller_price_${route.params.id}`"
              v-model.number="form.seller_price"
              type="number"
              step="0.01"
              min="0"
              autocomplete="off"
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
          <Label :for="`category_id_${route.params.id}`" value="Category" />
          <select
            :id="`category_id_${route.params.id}`"
            :name="`category_id_${route.params.id}`"
            v-model.number="form.category_id"
            autocomplete="off"
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
          <Label :for="`supplier_id_${route.params.id}`" value="Supplier" />
          <select
            :id="`supplier_id_${route.params.id}`"
            :name="`supplier_id_${route.params.id}`"
            v-model.number="form.supplier_id"
            autocomplete="off"
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
          <Label :for="`date_created_${route.params.id}`" value="Creation Date" />
          <Input
            :id="`date_created_${route.params.id}`"
            :name="`date_created_${route.params.id}`"
            v-model="form.date_created"
            type="date"
            autocomplete="off"
            required
          />
          <p v-if="errors.date_created" class="mt-1 text-sm text-red-600">
            {{ errors.date_created }}
          </p>
        </div>
        <div>
          <Label :for="`date_order_${route.params.id}`" value="Order Date" />
          <Input
            :id="`date_order_${route.params.id}`"
            :name="`date_order_${route.params.id}`"
            v-model="form.date_order"
            type="date"
            autocomplete="off"
          />
          <p v-if="errors.date_order" class="mt-1 text-sm text-red-600">
            {{ errors.date_order }}
          </p>
        </div>
        <div>
          <Label :for="`date_arrive_${route.params.id}`" value="Arrival Date" />
          <Input
            :id="`date_arrive_${route.params.id}`"
            :name="`date_arrive_${route.params.id}`"
            v-model="form.date_arrive"
            type="date"
            autocomplete="off"
          />
          <p v-if="errors.date_arrive" class="mt-1 text-sm text-red-600">
            {{ errors.date_arrive }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          :to="`/hardware-components/${route.params.id}`" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Updating...' : 'Update Component' }}
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

// Fetch component data
const { data: componentData } = await useLazyAsyncData(`component-${route.params.id}`, () =>
  api(`/hardware-components/${route.params.id}`, { method: 'GET' })
)

// Fetch categories and suppliers
const { data: categoriesData } = await useAsyncData('categories', () =>
  api('/categories', { method: 'GET' })
)
const { data: suppliersData } = await useAsyncData('suppliers', () =>
  api('/suppliers', { method: 'GET' })
)

const component = computed(() => componentData.value?.data || componentData.value)
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
  date_created: '',
  date_order: '',
  date_arrive: ''
})

// Populate form when component data loads
watch(component, (newComponent) => {
  if (newComponent) {
    form.component_reference_number = newComponent.component_reference_number
    form.component_name = newComponent.component_name
    form.brand = newComponent.brand
    form.model = newComponent.model
    form.specifications = newComponent.specifications
    form.retail_price = newComponent.retail_price
    form.seller_price = newComponent.seller_price
    form.category_id = newComponent.category_id
    form.supplier_id = newComponent.supplier_id
    form.date_created = newComponent.date_created ? newComponent.date_created.split('T')[0] : ''
    form.date_order = newComponent.date_order ? newComponent.date_order.split('T')[0] : ''
    form.date_arrive = newComponent.date_arrive ? newComponent.date_arrive.split('T')[0] : ''
  }
}, { immediate: true })

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    // Format dates properly
    const payload = {
      ...form,
      date_created: form.date_created || null,
      date_order: form.date_order || null,
      date_arrive: form.date_arrive || null
    }
    
    const response = await api(`/hardware-components/${route.params.id}`, {
      method: 'PATCH',
      body: payload
    })
    
    console.log('Update response:', response)
    
    // Redirect to show page with success message
    await router.push({
      path: `/hardware-components/${route.params.id}`,
      query: { success: 'Hardware component updated successfully' }
    })
  } catch (error: any) {
    console.error('Failed to update component:', error)
    
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
        general: error.data?.message || error.message || 'Failed to update component. Please try again.' 
      }
    }
  } finally {
    loading.value = false
  }
}
</script>

