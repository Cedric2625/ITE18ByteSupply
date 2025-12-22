<script setup lang="ts">
interface Props {
  type?: 'success' | 'error' | 'warning' | 'info'
  show?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  show: true
})

const emit = defineEmits<{
  'update:show': [value: boolean]
}>()

const alertClasses = computed(() => {
  const types = {
    success: 'bg-green-100 border-green-400 text-green-700',
    error: 'bg-red-100 border-red-400 text-red-700',
    warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
    info: 'bg-blue-100 border-blue-400 text-blue-700'
  }
  return types[props.type]
})

const close = () => {
  emit('update:show', false)
}
</script>

<template>
  <div 
    v-if="show"
    :class="[
      'border px-4 py-3 rounded relative max-w-7xl mx-auto mt-4',
      alertClasses
    ]"
    role="alert"
  >
    <span class="block sm:inline">
      <slot />
    </span>
    <button 
      @click="close" 
      class="absolute top-0 bottom-0 right-0 px-4 py-3"
    >
      <span class="sr-only">Close</span>
      <i class="fas fa-times"></i>
    </button>
  </div>
</template>

