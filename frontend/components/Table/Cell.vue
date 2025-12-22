<script setup lang="ts">
interface Props {
  colspan?: number
  class?: string
}

const props = defineProps<Props>()
</script>

<template>
  <td 
    :colspan="props.colspan"
    :class="[
      'px-6 py-4 whitespace-nowrap text-sm text-gray-500',
      props.class
    ]"
  >
    <slot />
  </td>
</template>

