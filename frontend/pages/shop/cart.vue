<template>
  <div class="bg-white rounded-lg shadow p-4">
    <div v-if="!cart || cart.length === 0" class="text-center py-12">
      <p class="text-gray-500">Your cart is empty.</p>
      <NuxtLink to="/shop" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
        Continue Shopping
      </NuxtLink>
    </div>
    <div v-else>
      <Table>
        <TableHead>
          <TableHeading>Item</TableHeading>
          <TableHeading>Price</TableHeading>
          <TableHeading>Quantity</TableHeading>
          <TableHeading>Subtotal</TableHeading>
          <TableHeading></TableHeading>
        </TableHead>
        <tbody>
          <TableRow 
            v-for="(item, index) in cart" 
            :key="item.id"
            :even="index % 2 === 0"
          >
            <TableCell class="font-medium">{{ item.name }}</TableCell>
            <TableCell>${{ formatCurrency(item.price) }}</TableCell>
            <TableCell>{{ item.quantity }}</TableCell>
            <TableCell>${{ formatCurrency(item.price * item.quantity) }}</TableCell>
            <TableCell>
              <button 
                @click="removeItem(item.id)"
                class="text-red-600 hover:text-red-800"
                :disabled="removing"
              >
                Remove
              </button>
            </TableCell>
          </TableRow>
        </tbody>
      </Table>
      <div class="mt-4 flex items-center justify-between">
        <div class="text-xl font-bold">Total: ${{ formatCurrency(total) }}</div>
        <button
          @click.stop.prevent="goToCheckout"
          type="button"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
          :disabled="cart.length === 0 || removing || navigating"
          :title="cart.length === 0 ? 'Add items to cart first' : 'Proceed to checkout'"
        >
          <span v-if="!navigating && !removing">Checkout</span>
          <span v-else>Processing...</span>
        </button>
      </div>
    </div>
    
    <!-- Confirm Remove Modal -->
    <ConfirmModal
      :show="showRemoveModal"
      message="Are you sure you want to remove this item?"
      :details="removeModalDetails"
      @confirm="confirmRemove"
      @cancel="cancelRemove"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'

interface CartItem {
  id: number
  name: string
  price: number
  quantity: number
}

// @ts-ignore - definePageMeta is auto-imported by Nuxt (will be available after .nuxt regeneration)
definePageMeta({
  middleware: 'auth'
})

const { api } = useApi()
const router = useRouter()

const cart = ref<CartItem[]>([])
const removing = ref(false)
const navigating = ref(false) // Prevent multiple navigation attempts
const showRemoveModal = ref(false)
const itemToRemove = ref<CartItem | null>(null)
const pendingItemId = ref<number | null>(null)

// Fetch cart from localStorage (client-side cart management)
onMounted(async () => {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem('cart')
    cart.value = stored ? JSON.parse(stored) : []
    
    // Verify stock availability for items in cart
    await verifyStockAvailability()
  }
})

// Verify stock availability for all items in cart
const verifyStockAvailability = async () => {
  if (cart.value.length === 0) return
  
  interface StockCheck {
    item: CartItem
    availableStock: number
    requestedQuantity: number
  }
  
  try {
    // Fetch current stock for each item
    const stockChecks = await Promise.all(
      cart.value.map(async (item: CartItem): Promise<StockCheck> => {
        try {
          const response = await api(`/hardware-components/${item.id}`, { method: 'GET' })
          const component = response.data || response
          return {
            item,
            availableStock: component.stock_quantity || 0,
            requestedQuantity: item.quantity || 1
          }
        } catch (error) {
          console.warn(`Failed to check stock for item ${item.id}:`, error)
          return {
            item,
            availableStock: 0,
            requestedQuantity: item.quantity || 1
          }
        }
      })
    )
    
    // Filter out items with insufficient stock
    const itemsToRemove: CartItem[] = []
    stockChecks.forEach((check: StockCheck) => {
      if (check.availableStock < check.requestedQuantity) {
        itemsToRemove.push(check.item)
        console.warn(`Insufficient stock for ${check.item.name}: Available ${check.availableStock}, Requested ${check.requestedQuantity}`)
      }
    })
    
    // Remove items with insufficient stock
    if (itemsToRemove.length > 0) {
      cart.value = cart.value.filter((item: CartItem) => 
        !itemsToRemove.some((removeItem: CartItem) => removeItem.id === item.id)
      )
      
      const itemNames = itemsToRemove.map((item: CartItem) => item.name).join(', ')
      alert(`⚠️ Some items were removed from your cart due to insufficient stock:\n\n${itemNames}\n\nPlease check availability and add them again if stock becomes available.`)
    }
  } catch (error) {
    console.error('Error verifying stock:', error)
  }
}

// Watch for cart changes and sync to localStorage
watch(cart, (newCart: CartItem[]) => {
  if (typeof window !== 'undefined') {
    localStorage.setItem('cart', JSON.stringify(newCart))
    // Trigger cart count update in layout (via custom event)
    window.dispatchEvent(new CustomEvent('cart-updated'))
  }
}, { deep: true })

const total = computed(() => {
  return cart.value.reduce((sum: number, item: CartItem) => {
    return sum + (item.price * item.quantity)
  }, 0)
})

const removeModalDetails = computed(() => {
  if (itemToRemove.value) {
    return `This will remove "${itemToRemove.value.name}" from your cart.`
  }
  return ''
})

const goToCheckout = async (e?: Event) => {
  // Prevent multiple clicks
  if (navigating.value) {
    return
  }
  
  if (e) {
    e.preventDefault()
    e.stopPropagation()
  }
  
  if (cart.value.length === 0) {
    alert('Your cart is empty. Please add items to your cart before checking out.')
    return
  }
  
  navigating.value = true
  
  try {
    // Use router.push to maintain auth state (no page reload)
    await router.push('/shop/checkout')
  } catch (error: any) {
    console.error('Navigation error:', error)
    alert('Failed to navigate to checkout. Please try again.')
    navigating.value = false
  }
}

const removeItem = async (itemId: number) => {
  // Find the item to show in the modal
  const item = cart.value.find((item: CartItem) => item.id === itemId)
  if (!item) return
  
  // Store the item and show modal
  itemToRemove.value = item
  pendingItemId.value = itemId
  showRemoveModal.value = true
}

const confirmRemove = async () => {
  if (pendingItemId.value === null) {
    showRemoveModal.value = false
    itemToRemove.value = null
    return
  }

  const itemId = pendingItemId.value
  showRemoveModal.value = false
  itemToRemove.value = null
  pendingItemId.value = null

  removing.value = true
  try {
    // Remove from local cart
    cart.value = cart.value.filter((item: CartItem) => item.id !== itemId)
    
    // Optionally call API to sync (if needed)
    try {
      await api(`/shop/remove/${itemId}`, {
        method: 'DELETE'
      })
    } catch (error) {
      // API call failed but local cart is updated
      console.warn('Failed to sync cart removal to server:', error)
    }
  } catch (error) {
    console.error('Failed to remove item:', error)
    alert('Failed to remove item. Please try again.')
  } finally {
    removing.value = false
  }
}

const cancelRemove = () => {
  showRemoveModal.value = false
  itemToRemove.value = null
  pendingItemId.value = null
}

const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}
</script>

