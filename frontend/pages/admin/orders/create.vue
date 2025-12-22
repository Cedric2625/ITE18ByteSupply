<template>
  <div class="max-w-4xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label for="buyer_id" value="Buyer" />
          <select
            id="buyer_id"
            v-model.number="form.buyer_id"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          >
            <option value="">Select Buyer</option>
            <option
              v-for="buyer in buyers"
              :key="buyer.id"
              :value="buyer.id"
            >
              {{ buyer.buyer_name }} ({{ buyer.buyer_number }})
            </option>
          </select>
          <p v-if="errors.buyer_id" class="mt-1 text-sm text-red-600">
            {{ errors.buyer_id }}
          </p>
        </div>

        <div>
          <Label for="admin_id" value="Admin" />
          <select
            id="admin_id"
            v-model.number="form.admin_id"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          >
            <option value="">Select Admin</option>
            <option
              v-for="admin in admins"
              :key="admin.id"
              :value="admin.id"
            >
              {{ admin.username }} ({{ admin.role }})
            </option>
          </select>
          <p v-if="errors.admin_id" class="mt-1 text-sm text-red-600">
            {{ errors.admin_id }}
          </p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <Label for="order_date" value="Order Date" />
          <Input
            id="order_date"
            v-model="form.order_date"
            type="date"
            required
          />
          <p v-if="errors.order_date" class="mt-1 text-sm text-red-600">
            {{ errors.order_date }}
          </p>
        </div>

        <div>
          <Label for="payment_method" value="Payment Method" />
          <select
            id="payment_method"
            v-model="form.payment_method"
            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            required
          >
            <option value="">Select Payment Method</option>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="cash">Cash</option>
          </select>
          <p v-if="errors.payment_method" class="mt-1 text-sm text-red-600">
            {{ errors.payment_method }}
          </p>
        </div>
      </div>

      <!-- Components Section -->
      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-medium text-gray-900">Order Components</h3>
          <button
            type="button"
            @click="addComponent"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            <i class="fas fa-plus mr-2"></i>Add Component
          </button>
        </div>

        <div
          v-for="(component, index) in form.selected_components"
          :key="index"
          class="bg-gray-50 p-4 rounded-lg space-y-4"
        >
          <div class="flex justify-between items-center">
            <h4 class="text-sm font-medium text-gray-700">Component #{{ index + 1 }}</h4>
            <button
              type="button"
              @click="removeComponent(index)"
              class="text-red-600 hover:text-red-900"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <Label :for="`component_${index}`" value="Hardware Component" />
              <select
                :id="`component_${index}`"
                v-model.number="component.component_id"
                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                required
              >
                <option value="">Select Component</option>
                <option
                  v-for="hwComponent in hardwareComponents"
                  :key="hwComponent.id"
                  :value="hwComponent.id"
                >
                  {{ hwComponent.component_name }} ({{ hwComponent.component_reference_number }}) - ${{ formatCurrency(hwComponent.retail_price) }}
                </option>
              </select>
            </div>

            <div>
              <Label :for="`quantity_${index}`" value="Quantity" />
              <Input
                :id="`quantity_${index}`"
                v-model.number="component.quantity"
                type="number"
                min="1"
                required
              />
            </div>
          </div>
        </div>

        <div v-if="form.selected_components.length === 0" class="text-center py-4 text-gray-500">
          No components added yet. Click "Add Component" to start building your order.
        </div>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          to="/admin/orders" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Order' }}
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

// Fetch data
const { data: buyersData } = await useAsyncData('buyers', () =>
  api('/buyers', { method: 'GET' })
)
const { data: adminsData } = await useAsyncData('admins', () =>
  api('/admins', { method: 'GET' })
)
const { data: componentsData } = await useAsyncData('hardware-components', () =>
  api('/hardware-components', { method: 'GET' })
)

const buyers = computed(() => buyersData.value?.data || [])
const admins = computed(() => adminsData.value?.data || [])
const hardwareComponents = computed(() => componentsData.value?.data || [])

const form = reactive({
  buyer_id: null as number | null,
  admin_id: null as number | null,
  order_date: new Date().toISOString().split('T')[0],
  payment_method: '',
  selected_components: [] as Array<{ component_id: number | null; quantity: number }>
})

const addComponent = () => {
  form.selected_components.push({
    component_id: null,
    quantity: 1
  })
}

const removeComponent = (index: number) => {
  form.selected_components.splice(index, 1)
}

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/orders', {
      method: 'POST',
      body: form
    })
    
    await router.push('/admin/orders')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create order' }
    }
  } finally {
    loading.value = false
  }
}
</script>

