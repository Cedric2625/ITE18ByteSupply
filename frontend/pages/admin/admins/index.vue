<template>
  <div>
    <!-- Success/Error Messages -->
    <div v-if="flashMessage" :class="[
      'mb-4 px-4 py-3 rounded relative max-w-7xl mx-auto',
      flashMessageType === 'success' 
        ? 'bg-green-100 border border-green-400 text-green-700' 
        : 'bg-red-100 border border-red-400 text-red-700'
    ]" role="alert">
      <span class="block sm:inline">{{ flashMessage }}</span>
      <button @click="clearFlashMessage" class="absolute top-0 bottom-0 right-0 px-4 py-3">
        <i class="fas fa-times"></i>
      </button>
    </div>

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-semibold flex items-center">
        <i class="fas fa-user-shield mr-3 text-red-500"></i>
        Admin List
      </h2>
      <NuxtLink to="/admin/admins/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>Add New Admin
      </NuxtLink>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <Table>
        <TableHead>
          <TableHeading>Username</TableHeading>
          <TableHeading>Role</TableHeading>
          <TableHeading>Created At</TableHeading>
          <TableHeading>Actions</TableHeading>
        </TableHead>
        <tbody>
          <TableRow 
            v-for="(admin, index) in admins" 
            :key="admin.id"
            :even="index % 2 === 0"
          >
            <TableCell>{{ admin.username }}</TableCell>
            <TableCell>
              <span 
                :class="[
                  'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                  admin.role === 'system_admin' ? 'bg-red-100 text-red-800' :
                  admin.role === 'supply_admin' ? 'bg-green-100 text-green-800' :
                  'bg-blue-100 text-blue-800'
                ]"
              >
                {{ admin.role }}
              </span>
            </TableCell>
            <TableCell>{{ formatDate(admin.created_at) }}</TableCell>
            <TableCell>
              <div class="flex items-center space-x-3">
                <NuxtLink 
                  :to="`/admin/admins/${admin.id}`" 
                  class="text-blue-600 hover:text-blue-900 transition-colors"
                  title="View"
                >
                  <i class="fas fa-eye"></i>
                </NuxtLink>
                <button 
                  @click="() => showDeleteConfirm(admin)" 
                  class="text-red-600 hover:text-red-900 transition-colors"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </TableCell>
          </TableRow>
          <TableRow v-if="!pending && admins.length === 0">
            <TableCell colspan="4" class="text-center py-8">
              <div class="flex flex-col items-center justify-center space-y-2">
                <i class="fas fa-users text-gray-400 text-5xl"></i>
                <p class="text-gray-500 text-lg">No admins found.</p>
                <NuxtLink to="/admin/admins/create" class="text-blue-500 hover:text-blue-700">
                  Add your first admin
                </NuxtLink>
              </div>
            </TableCell>
          </TableRow>
        </tbody>
      </Table>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmModal
      :show="showConfirmModal"
      :message="confirmMessage"
      :details="confirmDetails"
      @confirm="executeDelete"
      @cancel="cancelDelete"
    />

    <!-- Loading Modal -->
    <LoadingModal
      :show="isLoading || pending"
      :message="loadingMessage"
      :subMessage="loadingSubMessage"
    />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin']
})

const { api } = useApi()
const router = useRouter()
const { isLoading, loadingMessage, loadingSubMessage, showLoading, hideLoading, withLoading } = useLoading()

const deleting = ref(false)
const showConfirmModal = ref(false)
const adminToDelete = ref<any>(null)
const confirmMessage = ref('')
const confirmDetails = ref('')
const flashMessage = ref('')
const flashMessageType = ref<'success' | 'error'>('success')

const { data: adminsData, pending, refresh } = await useLazyAsyncData('admins', () =>
  api<Array<any>>('/admins', { method: 'GET' })
)

const admins = computed(() => {
  if (Array.isArray(adminsData.value)) {
    return adminsData.value
  }
  return adminsData.value?.data || []
})

const showDeleteConfirm = (admin: any) => {
  adminToDelete.value = admin
  confirmMessage.value = 'Are you sure you want to delete this admin?'
  confirmDetails.value = `Username: ${admin.username}`
  showConfirmModal.value = true
}

const cancelDelete = () => {
  showConfirmModal.value = false
  adminToDelete.value = null
  confirmMessage.value = ''
  confirmDetails.value = ''
}

const executeDelete = async () => {
  if (!adminToDelete.value) return

  showConfirmModal.value = false
  deleting.value = true

  try {
    await withLoading(
      async () => {
        await api(`/admins/${adminToDelete.value.id}`, {
          method: 'DELETE'
        })
        
        flashMessage.value = `Admin "${adminToDelete.value.username}" deleted successfully.`
        flashMessageType.value = 'success'
        await refresh()
        
        setTimeout(clearFlashMessage, 5000)
      },
      'Deleting admin...',
      'Please wait'
    )
  } catch (error: any) {
    console.error('Failed to delete admin:', error)
    const errorMsg = error.data?.message || 'Failed to delete admin. Please try again.'
    flashMessage.value = errorMsg
    flashMessageType.value = 'error'
    setTimeout(clearFlashMessage, 5000)
  } finally {
    deleting.value = false
    adminToDelete.value = null
  }
}

const clearFlashMessage = () => {
  flashMessage.value = ''
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

