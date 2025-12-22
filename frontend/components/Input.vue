<script setup lang="ts">
interface Props {
  id?: string
  name?: string
  type?: string
  modelValue?: string | number
  placeholder?: string
  required?: boolean
  min?: number
  max?: number
  maxlength?: number
  step?: number | string
  autocomplete?: string
  autofocus?: boolean
  disabled?: boolean
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  required: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})
</script>

<template>
  <input
    :id="id"
    :name="name"
    :type="type"
    :placeholder="placeholder"
    :required="required"
    :min="min"
    :max="max"
    :maxlength="maxlength"
    :step="step"
    :autocomplete="autocomplete"
    :autofocus="autofocus"
    :disabled="disabled"
    :value="modelValue"
    @input="value = ($event.target as HTMLInputElement).value"
    :class="[
      'mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500',
      props.class
    ]"
  />
</template>

