<script setup lang="ts">
interface Props {
  for?: string
  value?: string
}

const props = defineProps<Props>()
</script>

<template>
  <label 
    :for="props.for" 
    class="block text-sm font-medium text-gray-700"
  >
    <slot>{{ value }}</slot>
  </label>
</template>

