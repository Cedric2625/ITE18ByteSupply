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
          <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
              <i :class="[
                'fas text-2xl',
                type === 'error' ? 'fa-exclamation-circle text-red-500' : 'fa-info-circle text-blue-500'
              ]"></i>
            </div>
            <h3 :class="[
              'text-lg font-semibold ml-3',
              type === 'error' ? 'text-red-800' : 'text-blue-800'
            ]">{{ title }}</h3>
          </div>
          <p :class="[
            'text-gray-700 mb-4',
            type === 'error' ? 'text-red-700' : 'text-blue-700'
          ]">{{ message }}</p>
          <div class="flex justify-end">
            <button
              @click="handleClose"
              type="button"
              :class="[
                'px-4 py-2 rounded transition-colors',
                type === 'error' 
                  ? 'bg-red-600 text-white hover:bg-red-700' 
                  : 'bg-blue-600 text-white hover:bg-blue-700'
              ]"
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
  title: string
  message: string
  type?: 'error' | 'info'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info'
})

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

