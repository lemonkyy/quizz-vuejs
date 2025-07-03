<script setup lang="ts">
import { computed } from 'vue';
const modelValue = defineModel({required: true, default: ''});

const props = defineProps({
    id: { type: String },
    label: { type: String, default: '' },
    style: { type: String, default: 'neutral' },
    transparent: { type: Boolean, default: false },
    withoutborder: { type: Boolean, default: false },
    dashedborder: { type: Boolean, default: false },
    className: { type: String, default: '' },
});

const labelClasses = computed(() => {
  if (props.style === 'secondary') {
    return 'text-purple-800';
  }
  return 'text-blue-800';
});

const inputClasses = computed(() => {
  let classes = [];

  if (props.style === 'secondary') {
    classes.push('text-black bg-white text-purple-500 hover:text-purple-600 outline-purple-800 focus:outline-purple-400');
  } else {
    classes.push('text-black bg-white text-blue-500 hover:text-blue-600 outline-blue-800 focus:outline-blue-400');
  }

  if (props.withoutborder) {
    classes.push('outline-none');
  } else if (props.dashedborder) {
    classes.push('outline-2 outline-dashed outline-current');
  } else {
    classes.push('outline outline-transparent');
  }

  if (props.transparent) {
    classes = classes.filter(c => !(c.startsWith('bg-') || c.startsWith('border-')));
    classes.push('bg-transparent');
  }

  return classes;
});
</script>

<template>
  <div>
    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
    <select
      :id="id"
      v-model="modelValue"
      :class="[
        'w-full px-4 py-2 rounded-md transition-colors duration-200',
        inputClasses,
        className,
      ]"
    >
      <slot></slot>
    </select>
  </div>
</template>
