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
          autofocus
        />
        <p v-if="errors.username" class="mt-1 text-sm text-red-600">
          {{ errors.username }}
        </p>
      </div>

      <div>
        <Label for="password" value="Password" />
        <Input
          id="password"
          v-model="form.password"
          type="password"
          required
        />
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password }}
        </p>
      </div>

      <div>
        <Label for="password_confirmation" value="Confirm Password" />
        <Input
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          required
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
          to="/admin/admins" 
          class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400"
        >
          Cancel
        </NuxtLink>
        <Button type="submit" :disabled="loading">
          {{ loading ? 'Creating...' : 'Create Admin' }}
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

const form = reactive({
  username: '',
  password: '',
  password_confirmation: '',
  role: 'admin'
})

const loading = ref(false)
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    await api('/admins', {
      method: 'POST',
      body: form
    })
    
    await router.push('/admin/admins')
  } catch (error: any) {
    if (error.data?.errors) {
      errors.value = error.data.errors
    } else {
      errors.value = { general: error.data?.message || 'Failed to create admin' }
    }
  } finally {
    loading.value = false
  }
}
</script>

