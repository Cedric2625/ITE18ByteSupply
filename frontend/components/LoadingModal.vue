<template>
  <Teleport to="body">
    <Transition name="modal">
      <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0;"
      >
        <div 
          class="bg-white rounded-lg shadow-lg p-8 mx-4 flex flex-col items-center justify-center min-w-[200px]"
          @click.stop
        >
          <div class="relative w-16 h-16 mb-4">
            <div class="absolute inset-0 border-4 border-blue-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-500 rounded-full border-t-transparent animate-spin"></div>
          </div>
          <p class="text-gray-700 text-lg font-medium">{{ message || 'Loading...' }}</p>
          <p v-if="subMessage" class="text-gray-500 text-sm mt-2">{{ subMessage }}</p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  message?: string
  subMessage?: string
}

defineProps<Props>()
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

