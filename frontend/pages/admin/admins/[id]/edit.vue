<template>
  <div class="max-w-2xl mx-auto">
    <form @submit.prevent="handleSubmit" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <div>
        <Label for="username" value="Username" />
        <Input
          id="username"
          v-model="form.username"
          type="text"
          required
        />
        <p v-if="errors.username" class="mt-1 text-sm text-red-600">
          {{ errors.username }}
        </p>
      </div>

      <div>
        <Label for="password" value="New Password (leave blank to keep current)" />
        <Input
          id="password"
          v-model="form.password"
          type="password"
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password }}
        </p>
      </div>

      <div>
        <Label for="password_confirmation" value="Confirm New Password" />
        <Input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
        />
      </div>

      <div>
        <Label for="role" value="Role" />
        <select 
          id="role" 
          v-model="form.role"
          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
        >
          <option value="admin">Admin</option>
          <option value="system_admin">System Admin</option>
          <option value="supply_admin">Supply Admin</option>
        </select>
        <p v-if="errors.role" class="mt-1 text-sm text-red-600">
          {{ errors.role }}
        </p>
      </div>

      <div class="flex items-center justify-end space-x-3">
        <NuxtLink 
          :to="`/admin/admins/${route.params.id}`" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Updating...' : 'Update Admin' }}
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'

// @ts-ignore - definePageMeta is auto-imported by Nuxt
definePageMeta({
  middleware: ['auth', 'admin']
})

const route = useRoute()
const { api } = useApi()
const router = useRouter()

// @ts-ignore - useLazyAsyncData is auto-imported by Nuxt
const { data: adminData } = await useLazyAsyncData(`admin-${route.params.id}`, () =>
  api(`/admins/${route.params.id}`, { method: 'GET' })
)

const admin = computed(() => adminData.value?.data || adminData.value)

const form = reactive({
  username: '',
  password: '',
  password_confirmation: '',
  role: 'admin'
})

// Populate form when admin data loads
watch(admin, (newAdmin: any) => {
  if (newAdmin) {
    form.username = newAdmin.username
    form.role = newAdmin.role
  }
}, { immediate: true })

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    const body: any = {
      username: form.username,
      role: form.role
    }
    
    // Only include password if provided
    if (form.password) {
      body.password = form.password
      body.password_confirmation = form.password_confirmation
    }

    await api(`/admins/${route.params.id}`, {
      method: 'PUT',
      body
    })
    
    await router.push(`/admin/admins/${route.params.id}`)
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to update admin' }
    }
  } finally {
    loading.value = false
  }
}
</script>

