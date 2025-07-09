<script setup lang="ts">
import { computed } from 'vue';

const modelValue = defineModel<boolean>({required: true, default: false});

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    className: { type: String, default: '' },
    checkboxLeft: { type: Boolean, default: false },
    round: { type: Boolean, default: false },
});

const checkboxClasses = computed(() => {
  const classes = [
    'h-5 w-5 border flex-shrink-0 flex items-center justify-center transition-colors duration-200 border-checkbox-border',
    props.className,
  ];

  if (props.round) {
    classes.push('rounded-full border-2');
  } else {
    classes.push('rounded');
  }

  if (modelValue.value) {
    classes.push('border-checkbox-tick');
    if (props.round) {
      classes.push('bg-white', 'border-2');
    } else {
      classes.push('bg-checkbox-tick');
    }
  } else {
    classes.push('bg-transparent');
  }

  return classes;
});
</script>

<template>
  <div class="flex items-center gap-5" :class="[checkboxLeft ? 'flex-row' : 'flex-row-reverse']">
    <label :for="id" class="flex items-center cursor-pointer">
      <div :class="checkboxClasses">
        <input
          type="checkbox"
          :id="id"
          v-model="modelValue"
          class="sr-only"
        />
        <div v-if="modelValue && round" class="h-2.5 w-2.5 bg-checkbox-tick rounded-full"></div>
        <svg v-else-if="modelValue" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-checkbox-tick-inside" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <span v-if="label" class="ml-2 text-checkbox-label">{{ label }}</span>
    </label>
  </div>
</template>