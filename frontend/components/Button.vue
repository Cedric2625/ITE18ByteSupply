<script setup lang="ts">
interface Props {
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  variant?: 'primary' | 'secondary' | 'danger' | 'orange'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'button',
  disabled: false,
  variant: 'primary'
})

const buttonClasses = computed(() => {
  const base = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'
  
  const variants = {
    primary: 'bg-gray-800',
    secondary: 'bg-gray-300 text-gray-700 hover:bg-gray-400 focus:ring-gray-300',
    danger: 'bg-red-500 hover:bg-red-600 focus:ring-red-300',
    orange: 'bg-orange-500 hover:bg-orange-600 focus:ring-orange-300'
  }
  
  return `${base} ${variants[props.variant]}` 
})
</script>

<template>
  <button 
    :type="type"
    :disabled="disabled"
    :class="buttonClasses"
  >
    <slot />
  </button>
</template>

