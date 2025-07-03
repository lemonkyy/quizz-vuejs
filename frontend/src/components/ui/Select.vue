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

const inputClasses = computed(() => {
  let classes = [];

  if (props.style === 'secondary') {
    classes.push('bg-purple-500 text-white hover:bg-purple-600 text-purple-500 hover:text-purple-600 border-purple-500 hover:border-purple-600');
  } else {
    classes.push('bg-blue-500 text-white hover:bg-blue-600 text-blue-500 hover:text-blue-600 border-blue-500 hover:border-blue-600');
  }

  if (props.withoutborder) {
    classes.push('border-none');
  } else if (props.dashedborder) {
    classes.push('border-2 border-dashed border-current');
  } else {
    classes.push('border border-transparent');
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
    <label v-if="label" :for="id">{{ label }}</label>
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
 }

  return classes;
});


</script>