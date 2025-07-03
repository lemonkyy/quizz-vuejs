<script setup lang="ts">
import { computed } from 'vue';

const modelValue = defineModel<boolean>({required: true, default: false});

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    style: { type: String, default: 'neutral' },
    className: { type: String, default: '' },
});

const checkClasses = computed(() => {
  let classes = [];

  if (props.style === 'secondary') {
    classes.push('text-purple-500 focus:ring-purple-500');
  } else {
    classes.push('text-blue-500 focus:ring-blue-500');
  }

  return classes;
});
</script>

<template>
  <div class="flex items-center">
    <input
      type="checkbox"
      :id="id"
      v-model="modelValue"
      :class="[
        'h-4 w-4 rounded border-gray-300',
        checkClasses,
        className,
      ]"
    />
    
    <label v-if="label" :for="id" class="ml-2 block text-sm text-gray-900">{{ label }}</label>
  </div>
</template>
