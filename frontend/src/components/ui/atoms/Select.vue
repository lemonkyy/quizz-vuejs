<script setup lang="ts">
import { computed, type PropType } from 'vue';
const modelValue = defineModel({required: true, default: ''});

type SelectStyle = 'primary' | 'secondary';

type DefaultOption = {
    value: string;
    label: string;
    disabled?: boolean;
};

const props = defineProps({
    id: { type: String },
    defaultOption: { type: Object as PropType<DefaultOption>, default: () => ({ label: 'Sélectionner une option...', value: '', disabled: true })},
    label: { type: String, default: '' },
    theme: { type: String as PropType<SelectStyle>, default: 'primary' },
    transparent: { type: Boolean, default: false },
    withoutBorder: { type: Boolean, default: false },
    dashedBorder: { type: Boolean, default: false },
    className: { type: String, default: '' },
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-purple-800';
  }
  return 'text-blue-800';
});

const inputClasses = computed(() => {
  let classes = [];

  if (props.theme === 'secondary') {
    classes.push('text-black bg-white text-purple-500 hover:text-purple-600 outline-purple-800 focus:outline-purple-400');
  } else {
    classes.push('text-black bg-white text-blue-500 hover:text-blue-600 outline-blue-800 focus:outline-blue-400');
  }

  if (props.withoutBorder) {
    classes.push('outline-none');
  } else if (props.dashedBorder) {
    classes.push('outline-2 outline-dashed');
  } else {
    classes.push('outline-2 outline-solid');
  }

  if (props.transparent) {
    classes = classes.filter(c => !(c.startsWith('bg-') || c.startsWith('outline-')));
    classes.push('bg-transparent');
  }

  if (props.label === '') {
    classes.push('w-full');
  }

  return classes;
});
</script>

<template>
  <div class="flex flex-row items-center gap-2 max-w-[500px]">
    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
    <select
      :id="id"
      v-model="modelValue"
      :class="[
        'px-3 py-2 rounded-md transition-colors duration-200',
        inputClasses,
        className,
      ]"
    >
      <option
        v-if="defaultOption"
        :value="defaultOption.value ?? ''"
        :disabled="defaultOption.disabled ?? true"
      >
        {{ defaultOption.label }}
      </option>
      <slot></slot>
    </select>
  </div>
</template>
