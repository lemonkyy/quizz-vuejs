<template>
  <div class="space-y-4">
    <div
      v-for="(option, idx) in options"
      :key="idx"
      class="flex items-center gap-4 px-4 py-3 rounded-md border cursor-pointer transition"
      :class="{
        'border-yellow-400 bg-yellow-50': modelValue === option,
        'border-[#D9D6C7]': modelValue !== option
      }"
      @click="internalValue = option"
    >
      <div
        class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
        :class="{
          'border-yellow-400': modelValue === option,
          'border-[#D9D6C7]': modelValue !== option
        }"
      >
        <div
          v-if="modelValue === option"
          class="w-2.5 h-2.5 bg-yellow-400 rounded-full"
        ></div>
      </div>
      <span class="text-gray-900">{{ option }}</span>
    </div>
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
