<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50"
        @click.self="handleClose"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;"
      >
        <div 
          class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 mx-4"
          @click.stop
        >
          <div class="flex items-center justify-center mb-4">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
              <i class="fas fa-check text-green-500 text-2xl"></i>
            </div>
          </div>
          <h3 class="text-lg font-semibold text-center mb-2 text-gray-800">{{ title || 'Success!' }}</h3>
          <p class="text-gray-700 text-center mb-6">{{ message }}</p>
          <div class="flex justify-center">
            <button
              @click="handleClose"
              type="button"
              class="px-6 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
            >
              OK
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
  title?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
}>()

const handleClose = () => {
  emit('close')
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

