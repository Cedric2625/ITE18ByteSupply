<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Category Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Details and associated hardware components.
          </p>
        </div>
      </div>
      <div class="border-t border-gray-200" v-if="category">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Category name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ category.category_name }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Total components</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ category.hardware_components_count || 0 }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Created at</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ formatDateTime(category.created_at) }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Last updated</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ formatDateTime(category.updated_at) }}
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Hardware Components Section -->
    <div v-if="components && components.length > 0" class="mt-8">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Hardware Components</h4>
      <div class="bg-white rounded shadow overflow-hidden">
        <Table>
          <TableHead>
            <TableHeading>Reference #</TableHeading>
            <TableHeading>Name</TableHeading>
            <TableHeading>Brand</TableHeading>
            <TableHeading>Price</TableHeading>
            <TableHeading>Actions</TableHeading>
          </TableHead>
          <tbody>
            <TableRow 
              v-for="(hardwareComponent, index) in components" 
              :key="hardwareComponent.id"
              :even="index % 2 === 0"
            >
              <TableCell>{{ hardwareComponent.component_reference_number }}</TableCell>
              <TableCell>{{ hardwareComponent.component_name }}</TableCell>
              <TableCell>{{ hardwareComponent.brand }}</TableCell>
              <TableCell>${{ formatCurrency(hardwareComponent.retail_price) }}</TableCell>
              <TableCell>
                <NuxtLink 
                  :to="`/hardware-components/${hardwareComponent.id}`" 
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

    <div class="mt-6">
      <NuxtLink 
        to="/categories" 
        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
      >
        <i class="fas fa-arrow-left mr-2"></i>Back to List
      </NuxtLink>
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
  title: 'View Category',
  key: (route: any) => `category-view-${route.params.id}`
})

const { api } = useApi()
const route = useRoute()

const categoryId = route.params.id as string

// Fetch category with components
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: categoryData, pending } = await useLazyAsyncData(`category-${categoryId}`, async () => {
  const response = await api(`/categories/${categoryId}`, { method: 'GET' })
  return response
})

const category = computed(() => {
  const data = categoryData.value
  return data?.data || data
})

// Fetch components for this category
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: componentsData } = await useLazyAsyncData(`category-components-${categoryId}`, async () => {
  const response = await api(`/hardware-components/category/${categoryId}`, { method: 'GET' })
  return response.data || response || []
})

const components = computed(() => componentsData.value || [])

const formatDateTime = (date: string) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

