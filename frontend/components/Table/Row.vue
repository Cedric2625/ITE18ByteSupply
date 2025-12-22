<script setup lang="ts">
interface Props {
  even?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  even: false
})
</script>

<template>
  <tr :class="props.even ? 'bg-gray-50' : 'bg-white'">
    <slot />
  </tr>
</template>

