<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-semibold">Order Details</h2>
      <p class="text-gray-500 mt-2">Order #{{ order?.order_reference_number }}</p>
    </div>

    <!-- Order Information -->
    <div class="bg-white rounded shadow mb-6 p-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <span class="text-sm text-gray-500">Order Reference:</span>
          <p class="font-semibold">{{ order?.order_reference_number }}</p>
        </div>
        <div>
          <span class="text-sm text-gray-500">Order Date:</span>
          <p class="font-semibold">{{ formatDate(order?.order_date || order?.created_at || '') }}</p>
        </div>
        <div>
          <span class="text-sm text-gray-500">Status:</span>
          <span :class="[
            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
            getStatusClass(order?.shipping_status || '')
          ]">
            {{ formatStatus(order?.shipping_status || '') }}
          </span>
        </div>
        <div>
          <span class="text-sm text-gray-500">Total Amount:</span>
          <p class="font-semibold text-lg">${{ formatCurrency(order?.total_amount || 0) }}</p>
        </div>
        <div>
          <span class="text-sm text-gray-500">Payment Method:</span>
          <p class="font-semibold">{{ formatStatus(order?.payment_method || '') }}</p>
        </div>
        <div v-if="order?.tracking_number">
          <span class="text-sm text-gray-500">Tracking Number:</span>
          <p class="font-semibold">{{ order.tracking_number }}</p>
        </div>
      </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded shadow">
      <div class="p-4 border-b font-semibold">Order Items</div>
      <div class="p-4">
        <Table v-if="items && items.length > 0">
          <TableHead>
            <TableHeading>Component</TableHeading>
            <TableHeading>Brand</TableHeading>
            <TableHeading>Price</TableHeading>
            <TableHeading>Quantity</TableHeading>
            <TableHeading>Subtotal</TableHeading>
          </TableHead>
          <tbody>
            <TableRow 
              v-for="(item, index) in items" 
              :key="item.id"
              :even="index % 2 === 0"
            >
              <TableCell class="font-medium">{{ item.hardware_component?.component_name || 'N/A' }}</TableCell>
              <TableCell>{{ item.hardware_component?.brand || 'N/A' }}</TableCell>
              <TableCell>${{ formatCurrency(item.hardware_component?.retail_price || 0) }}</TableCell>
              <TableCell>{{ item.quantity }}</TableCell>
              <TableCell>${{ formatCurrency((item.hardware_component?.retail_price || 0) * item.quantity) }}</TableCell>
            </TableRow>
          </tbody>
        </Table>
        <div v-else class="text-center py-12">
          <p class="text-gray-500">No items found in this order.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface HardwareComponent {
  id: number
  component_name: string
  brand: string
  retail_price: number
  [key: string]: any
}

interface OrderItem {
  id: number
  quantity: number
  hardware_component?: HardwareComponent
  hardwareComponent?: HardwareComponent
  [key: string]: any
}

interface Order {
  id: number
  order_reference_number: string
  order_date?: string
  created_at?: string
  total_amount: number
  shipping_status: string
  payment_method?: string
  tracking_number?: string
  selectedComponents?: OrderItem[]
  selected_components?: OrderItem[]
  [key: string]: any
}

import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '~/composables/useApi'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: 'auth'
})

const { api } = useApi()
const route = useRoute()

const orderId = route.params.id as string

// Fetch order details
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: orderData, pending } = await useLazyAsyncData<Order>(`customer-order-${orderId}`, async () => {
  const response = await api<Order>(`/orders/${orderId}`, { method: 'GET' })
  return response
})

const order = computed<Order | null>(() => {
  const data = orderData.value
  if (!data) return null
  return (data as Order)
})

// Fetch order items
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: itemsData } = await useLazyAsyncData<OrderItem[] | { data?: OrderItem[] }>(`customer-order-items-${orderId}`, async () => {
  const response = await api<OrderItem[] | { data?: OrderItem[] }>(`/orders/${orderId}/items`, { method: 'GET' })
  return response
})

const items = computed<OrderItem[]>(() => {
  const data = itemsData.value
  if (!data) return []
  if (Array.isArray(data)) return data as OrderItem[]
  if (data && typeof data === 'object' && 'data' in data) {
    return (data.data || []) as OrderItem[]
  }
  return []
})

const getStatusClass = (status: string) => {
  if (!status) return 'bg-gray-100 text-gray-800'
  const classes: Record<string, string> = {
    completed: 'bg-green-100 text-green-800',
    shipped: 'bg-blue-100 text-blue-800',
    out_for_delivery: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    arrived_hub: 'bg-yellow-100 text-yellow-800',
    canceled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatStatus = (status: string) => {
  if (!status) return ''
  return status.replace(/_/g, ' ').split(' ').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const formatDate = (date: string) => {
  if (!date) return ''
  const d = new Date(date)
  return d.toLocaleDateString()
}
</script>

