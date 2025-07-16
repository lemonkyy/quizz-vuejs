<template>
    <div class="space-y-4">
      <label
        v-for="(option, idx) in options"
        :key="idx"
        class="flex items-center border rounded px-4 py-3 cursor-pointer transition 
               hover:bg-yellow-50"
        :class="{
          'border-yellow-400 bg-yellow-50': modelValue === option
        }"
      >
        <input
          type="radio"
          class="form-radio text-yellow-400 mr-3"
          :value="option"
          v-model="internalValue"
        />
        <span class="text-gray-800">{{ option }}</span>
      </label>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue'
  
  const props = defineProps({
    options: Array,
    modelValue: String
  })
  const emit = defineEmits(['update:modelValue'])
  
  const internalValue = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
  })
  </script>
  