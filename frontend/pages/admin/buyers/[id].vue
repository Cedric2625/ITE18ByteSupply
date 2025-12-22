<template>
  <div class="max-w-3xl mx-auto">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            Buyer Information
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Personal details and order history.
          </p>
        </div>
      </div>
      <div class="border-t border-gray-200" v-if="buyer">
        <dl>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Full name</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ buyer.buyer_name }}
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Buyer number</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ buyer.buyer_number }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Email address</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              <a :href="`mailto:${buyer.email}`" class="text-blue-600 hover:text-blue-900">
                {{ buyer.email }}
              </a>
            </dd>
          </div>
          <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Address</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ buyer.address }}
            </dd>
          </div>
          <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <dt class="text-sm font-medium text-gray-500">Total orders</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
              {{ buyer.orders?.length || 0 }}
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Orders Section -->
    <div v-if="buyer?.orders && buyer.orders.length > 0" class="mt-8">
      <h4 class="text-lg font-medium text-gray-900 mb-4">Order History</h4>
      <div class="bg-white rounded shadow overflow-hidden">
        <Table>
          <TableHead>
            <TableHeading>Order #</TableHeading>
            <TableHeading>Date</TableHeading>
            <TableHeading>Status</TableHeading>
            <TableHeading>Total</TableHeading>
            <TableHeading>Actions</TableHeading>
          </TableHead>
          <tbody>
            <TableRow 
              v-for="(order, index) in buyer.orders" 
              :key="order.id"
              :even="index % 2 === 0"
            >
              <TableCell>{{ order.order_reference_number }}</TableCell>
              <TableCell>{{ formatDate(order.order_date) }}</TableCell>
              <TableCell>
                <span 
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    getStatusClass(order.shipping_status)
                  ]"
                >
                  {{ capitalize(order.shipping_status) }}
                </span>
              </TableCell>
              <TableCell>${{ formatCurrency(order.total_amount) }}</TableCell>
              <TableCell>
                <NuxtLink 
                  :to="`/admin/orders/${order.id}`" 
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
        to="/admin/buyers" 
        class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
      >
        <i class="fas fa-arrow-left mr-2"></i>Back to List
      </NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin']
})

const route = useRoute()
const { api } = useApi()

const { data: buyerData } = await useLazyAsyncData(`buyer-${route.params.id}`, () =>
  api(`/buyers/${route.params.id}`, { method: 'GET' })
)

const buyer = computed(() => buyerData.value?.data || buyerData.value)

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

const capitalize = (str: string) => {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    delivered: 'bg-green-100 text-green-800',
    shipped: 'bg-blue-100 text-blue-800',
    processing: 'bg-yellow-100 text-yellow-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}
</script>

