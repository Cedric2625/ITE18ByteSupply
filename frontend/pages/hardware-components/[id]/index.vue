<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Component Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Details and specifications about the component.
          </p>
        </div>
      </div>
      <div class="border-t border-gray-200" v-if="component">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Reference number</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ component.component_reference_number }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Component name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ component.component_name }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Brand & Model</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ component.brand }} - {{ component.model }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Category</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <NuxtLink 
                :to="`/categories/${component.category?.id}`" 
                class="text-blue-600 hover:text-blue-900"
              >
                {{ component.category?.category_name }}
              </NuxtLink>
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <NuxtLink 
                :to="`/admin/suppliers/${component.supplier?.id}`" 
                class="text-blue-600 hover:text-blue-900"
              >
                {{ component.supplier?.supplier_name }}
              </NuxtLink>
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Specifications</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ component.specifications }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Pricing</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="space-y-1">
                <div>
                  <span class="font-medium">Retail Price:</span> ${{ formatCurrency(component.retail_price) }}
                </div>
                <div>
                  <span class="font-medium">Seller Price:</span> ${{ formatCurrency(component.seller_price) }}
                </div>
              </div>
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Dates</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <div class="space-y-1">
                <div>
                  <span class="font-medium">Created:</span> {{ formatDate(component.date_created) }}
                </div>
                <div v-if="component.date_order">
                  <span class="font-medium">Ordered:</span> {{ formatDate(component.date_order) }}
                </div>
                <div v-if="component.date_arrive">
                  <span class="font-medium">Arrived:</span> {{ formatDate(component.date_arrive) }}
                </div>
              </div>
            </dd>
          </div>
        </dl>
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
  title: 'View Component',
  key: (route: any) => `component-view-${route.params.id}`
})

const route = useRoute()
const { api } = useApi()

// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: componentData } = await useLazyAsyncData(`component-${route.params.id}`, () =>
  api(`/hardware-components/${route.params.id}`, { method: 'GET' })
)

const component = computed(() => {
  const data = componentData.value
  return data?.data || data
})

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDate = (date: string | null) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  })
}
</script>

