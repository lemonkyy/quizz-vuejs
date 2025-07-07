<script setup lang="ts">
import { computed, type PropType } from 'vue';
const modelValue = defineModel({required: true, default: ''});

type SelectStyle = 'primary' | 'secondary';
type DefaultOption = {
    value: string;
    label: string;
    disabled?: boolean;
};
type ButtonRadius = 'sm' | 'md' | 'lg';

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    theme: { type: String as PropType<SelectStyle>, default: 'primary' },
    rounded: { type: String as PropType<ButtonRadius>, default: 'md' },
    defaultOption: { type: Object as PropType<DefaultOption>, default: () => ({ label: 'Sélectionner une option...', value: '', disabled: true })},
    transparent: { type: Boolean, default: false },
    withoutBorder: { type: Boolean, default: false },
    dashedBorder: { type: Boolean, default: false },
    className: { type: String, default: '' },
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-alt';
  }
  return 'text-highlight';
});

const inputClasses = computed(() => {
  let classes = ['px-custom-x py-custom-y rounded-md transition-colors duration-200 cursor-pointer border-none'];

  if (props.theme === 'secondary') {
    classes.push('text-alt bg-neutral outline-highlight focus:outline-primary');
  } else {
    classes.push('text-highlight bg-white outline-secondary focus:outline-primary');
  }

  switch (props.rounded) {
    case 'sm':
      classes.push('rounded-custom-sm');
      break;
    case 'md':
      classes.push('rounded-custom-md');
      break;
    case 'lg':
      classes.push('rounded-custom-lg');
      break;
    default:
      classes.push('rounded-custom-md');
      break;
  }

  if (props.withoutBorder) {
    classes.push('outline-none focus:outline-1 focus:outline-solid');
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
