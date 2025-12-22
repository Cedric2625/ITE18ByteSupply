<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50"
        @click.self="handleCancel"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;"
      >
        <div 
          class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 mx-4"
          @click.stop
        >
          <h3 class="text-lg font-semibold mb-2">Confirm action</h3>
          <p class="text-gray-700 mb-1">{{ message }}</p>
          <p v-if="details" class="text-gray-500 text-sm mb-4">{{ details }}</p>
          <div class="flex justify-end space-x-2">
            <button
              @click="handleCancel"
              type="button"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors"
            >
              No
            </button>
            <button
              @click="handleConfirm"
              type="button"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
            >
              Yes
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  message: string
  details?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

const handleConfirm = () => {
  emit('confirm')
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>

