<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Supplier Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Contact details and supplied components.
          </p>
        </div>
      </div>
      <div class="border-t border-gray-200" v-if="supplier">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Supplier name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ supplier.supplier_name }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Contact person</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ supplier.contact_person }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Email address</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <a :href="`mailto:${supplier.email}`" class="text-blue-600 hover:text-blue-900">
                {{ supplier.email }}
              </a>
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Phone number</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ supplier.phone_number }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Total components</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ supplier.hardware_components?.length || 0 }}
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Hardware Components Section -->
    <div v-if="supplier?.hardware_components && supplier.hardware_components.length > 0" class="mt-8">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Supplied Components</h4>
      <div class="bg-white rounded shadow overflow-hidden">
        <Table>
          <TableHead>
            <TableHeading>Reference #</TableHeading>
            <TableHeading>Name</TableHeading>
            <TableHeading>Category</TableHeading>
            <TableHeading>Price</TableHeading>
            <TableHeading>Actions</TableHeading>
          </TableHead>
          <tbody>
            <TableRow 
              v-for="(component, index) in supplier.hardware_components" 
              :key="component.id"
              :even="index % 2 === 0"
            >
              <TableCell>{{ component.component_reference_number }}</TableCell>
              <TableCell>{{ component.component_name }}</TableCell>
              <TableCell>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                  {{ component.category?.category_name }}
                </span>
              </TableCell>
              <TableCell>${{ formatCurrency(component.retail_price) }}</TableCell>
              <TableCell>
                <NuxtLink 
                  :to="`/hardware-components/${component.id}`" 
                  class="text-blue-600 hover:text-blue-900"
                >
                  View Details
                </NuxtLink>
              </TableCell>
            </TableRow>
          </tbody>
        </Table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '~/composables/useApi'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: ['auth', 'admin'],
  title: 'View Supplier',
  key: (route: any) => `supplier-view-${route.params.id}`
})

const route = useRoute()
const { api } = useApi()

// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: supplierData } = await useLazyAsyncData(`supplier-${route.params.id}`, () =>
  api(`/suppliers/${route.params.id}`, { method: 'GET' })
)

const supplier = computed(() => {
  const data = supplierData.value
  return data?.data || data
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

