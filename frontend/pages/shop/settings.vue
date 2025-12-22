<template>
  <div class="space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold mb-4">Profile</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-500">Name</p>
          <p class="text-base font-medium text-gray-900">{{ buyer?.buyer_name || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Email</p>
          <p class="text-base font-medium text-gray-900">{{ buyer?.email || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500">Account Created</p>
          <p class="text-base font-medium text-gray-900">{{ formatDateTime(buyer?.created_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useApi } from '~/composables/useApi'
import { useAuth } from '~/composables/useAuth'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: 'auth'
})

const { api } = useApi()
const { user } = useAuth()

// Fetch buyer details
// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: buyerData, pending } = await useLazyAsyncData('buyer-settings', async () => {
  try {
    // Get current user info from API
    const response = await api<any>('/get-user', { method: 'GET' })
    
    console.log('Settings API response:', response)
    
    // The API returns { user_info: { buyer_name, email, created_at, ... } }
    if (response && response.user_info) {
      console.log('User info found:', response.user_info)
      return response.user_info
    }
    
    // Check if response itself is the user_info (some API formats)
    if (response && ((response as any).buyer_name || (response as any).email)) {
      console.log('Response is user_info:', response)
      return response
    }
    
    // Fallback to user from auth store
    console.log('Using auth store user:', user.value)
    return user.value
  } catch (error) {
    console.error('Failed to fetch buyer settings:', error)
    // Fallback to user from auth store
    return user.value
  }
})

const buyer = computed(() => {
  const data = buyerData.value
  const authUser = user.value
  console.log('Buyer computed - raw data:', data)
  console.log('Buyer computed - auth user:', authUser)
  
  // Handle different response structures
  let userInfo: any = null
  
  if (data) {
    userInfo = (data as any).user_info || data
  }
  
  // Fallback to auth store user if API data is not available
  if (!userInfo && authUser) {
    userInfo = authUser
  }
  
  if (!userInfo) {
    console.log('No buyer data available')
    return null
  }
  
  // Ensure we have the correct field names
  const buyerInfo = {
    buyer_name: userInfo?.buyer_name || userInfo?.name || null,
    email: userInfo?.email || null,
    created_at: userInfo?.created_at || null
  }
  
  console.log('Processed buyer info:', buyerInfo)
  return buyerInfo
})

const formatDateTime = (date: string | null) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}
</script>

