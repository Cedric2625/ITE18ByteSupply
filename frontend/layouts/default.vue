<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <!-- Logo -->
            <div class="flex-shrink-0">
              <NuxtLink to="/" class="text-white font-bold text-xl">
                ByteSupply
              </NuxtLink>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <!-- Admin Links -->
                <template v-if="isAdmin">
                  <NuxtLink 
                    to="/admin/dashboard" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/admin/dashboard') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Dashboard
                  </NuxtLink>
                  <NuxtLink 
                    to="/admin/admins" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/admin/admins') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Admins
                  </NuxtLink>
                  <NuxtLink 
                    to="/admin/buyers" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/admin/buyers') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Buyers
                  </NuxtLink>
                  <NuxtLink 
                    to="/categories" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/categories') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Categories
                  </NuxtLink>
                  <NuxtLink 
                    to="/admin/suppliers" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/admin/suppliers') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Suppliers
                  </NuxtLink>
                  <NuxtLink 
                    to="/hardware-components" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/hardware-components') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Hardware
                  </NuxtLink>
                  <NuxtLink 
                    to="/admin/orders" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/admin/orders') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Orders
                  </NuxtLink>
                </template>

                <!-- Buyer Links -->
                <template v-if="isBuyer">
                  <NuxtLink 
                    to="/shop" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/shop') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Shop
                  </NuxtLink>
                  <NuxtLink 
                    to="/shop/orders" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/shop/orders') && !isActive('/shop/orders/history')
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    My Orders
                  </NuxtLink>
                  <NuxtLink 
                    to="/shop/orders/history" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/shop/orders/history')
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Order History
                  </NuxtLink>
                  <NuxtLink 
                    to="/shop/cart" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium relative',
                      isActive('/shop/cart') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Cart
                    <span 
                      v-if="cartCount > 0"
                      class="absolute -top-1 -right-1 inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full"
                    >
                      {{ cartCount }}
                    </span>
                  </NuxtLink>
                  <NuxtLink 
                    to="/shop/settings" 
                    :class="[
                      'px-3 py-2 rounded-md text-sm font-medium',
                      isActive('/shop/settings') 
                        ? 'bg-gray-900 text-white' 
                        : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                    ]"
                  >
                    Settings
                  </NuxtLink>
                </template>

                <!-- Auth Links -->
                <template v-if="isAuthenticated">
                  <button 
                    @click="handleLogout" 
                    class="text-sm text-gray-300 hover:text-white px-3 py-2"
                  >
                    Logout
                  </button>
                </template>
                <template v-else>
                  <NuxtLink 
                    v-if="!isActive('/auth/login')"
                    to="/auth/login" 
                    class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium"
                  >
                    Login
                  </NuxtLink>
                </template>
              </div>
            </div>
          </div>

          <!-- Mobile menu button -->
          <div class="-mr-2 flex md:hidden">
            <button 
              @click="mobileMenuOpen = !mobileMenuOpen" 
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700"
            >
              <span class="sr-only">Open main menu</span>
              <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-show="mobileMenuOpen" class="md:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
          <!-- Same links as desktop but in mobile format -->
          <template v-if="isAdmin">
            <NuxtLink to="/admin/dashboard" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Dashboard</NuxtLink>
            <NuxtLink to="/admin/admins" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Admins</NuxtLink>
            <NuxtLink to="/admin/buyers" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Buyers</NuxtLink>
            <NuxtLink to="/categories" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Categories</NuxtLink>
            <NuxtLink to="/admin/suppliers" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Suppliers</NuxtLink>
            <NuxtLink to="/hardware-components" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Hardware</NuxtLink>
            <NuxtLink to="/admin/orders" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Orders</NuxtLink>
          </template>
          <template v-if="isBuyer">
            <NuxtLink to="/shop" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Shop</NuxtLink>
            <NuxtLink to="/shop/orders" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">My Orders</NuxtLink>
            <NuxtLink to="/shop/orders/history" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Order History</NuxtLink>
            <NuxtLink to="/shop/cart" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white relative pr-8">
              Cart
              <span v-if="cartCount > 0" class="absolute top-1 right-3 inline-flex items-center justify-center h-5 min-w-[1.25rem] px-1 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">
                {{ cartCount }}
              </span>
            </NuxtLink>
            <NuxtLink to="/shop/settings" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Settings</NuxtLink>
          </template>
          <template v-if="isAuthenticated">
            <button @click="handleLogout" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Logout</button>
          </template>
          <template v-else>
            <NuxtLink v-if="!isActive('/auth/login')" to="/auth/login" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Login</NuxtLink>
          </template>
        </div>
      </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
          <button 
            v-if="showBackButton"
            @click="goBack"
            class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200"
          >
            <i class="fas fa-arrow-left mr-2"></i> Back
          </button>
          <h1 class="text-3xl font-bold text-gray-900">
            <slot name="header">{{ pageTitle }}</slot>
          </h1>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <Alert 
      v-model:show="showSuccess" 
      type="success"
      v-if="successMessage"
    >
      {{ successMessage }}
    </Alert>

    <Alert 
      v-model:show="showError" 
      type="error"
      v-if="errorMessage"
    >
      {{ errorMessage }}
    </Alert>

    <!-- Page Content -->
    <main class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <slot />
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow mt-auto">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <p class="text-center text-gray-500 text-sm">
          &copy; {{ new Date().getFullYear() }} ByteSupply. All rights reserved.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const route = useRoute()
const router = useRouter()
const { isAuthenticated, isAdmin, logout, user } = useAuth()
const { successMessage, errorMessage, showSuccess, showError } = useFlash()

const mobileMenuOpen = ref(false)
// Get cart count from localStorage
const cartCount = ref(0)

const updateCartCount = () => {
  if (process.client) {
    try {
      const stored = localStorage.getItem('cart')
      if (stored) {
        const cart = JSON.parse(stored)
        cartCount.value = cart.reduce((sum: number, item: any) => sum + (item.quantity || 1), 0)
      } else {
        cartCount.value = 0
      }
    } catch (error) {
      console.error('Error reading cart from localStorage:', error)
      cartCount.value = 0
    }
  }
}

// Update cart count on mount and when route changes
onMounted(() => {
  updateCartCount()
  // Listen for cart updates from other components
  if (process.client) {
    window.addEventListener('cart-updated', updateCartCount)
    // Update cart count every second to catch changes from other tabs/pages
    setInterval(updateCartCount, 1000)
  }
})

// Watch for route changes to update cart count
watch(() => route.path, () => {
  updateCartCount()
})

// Cleanup event listener
onUnmounted(() => {
  if (process.client) {
    window.removeEventListener('cart-updated', updateCartCount)
  }
})
const isMounted = ref(false)

const isBuyer = computed(() => isAuthenticated.value && !isAdmin.value)

const pageTitle = computed(() => {
  // Extract title from route or use default
  return route.meta.title as string || 'ByteSupply'
})

const showBackButton = computed(() => {
  // Only show after mount to avoid hydration mismatch
  if (!isMounted.value) return false
  
  const noBackRoutes = ['/admin/dashboard', '/shop', '/auth/login', '/auth/register', '/']
  if (noBackRoutes.includes(route.path)) return false
  
  // Check if there's history to go back to
  if (process.client) {
    try {
      return window.history.length > 1
    } catch {
      return false
    }
  }
  return false
})

onMounted(() => {
  isMounted.value = true
})

const isActive = (path: string) => {
  return route.path.startsWith(path)
}

const goBack = () => {
  if (process.client) {
    if (window.history.length > 1) {
      router.back()
    } else {
      router.push('/')
    }
  }
}

const handleLogout = async () => {
  await logout()
}

// Flash messages (from query params or store) - using useFlash composable
const { flashSuccess, flashError } = useFlash()

watch(() => route.query.success, (val) => {
  if (val) {
    flashSuccess(val as string, 3000)
  }
})

watch(() => route.query.error, (val) => {
  if (val) {
    flashError(val as string, 3000)
  }
})
</script>
