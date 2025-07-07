<script setup lang="ts">
import { computed, ref, type PropType } from 'vue';

const modelValue = defineModel({required: true, default: ''});

type InputType = 'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search' | undefined;
type InputStyle = 'primary' | 'secondary';
type ButtonRadius = 'sm' | 'md' | 'lg';

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    theme: { type: String as PropType<InputStyle>, default: 'primary' },
    rounded: { type: String as PropType<ButtonRadius>, default: 'md' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    withoutBorder: { type: Boolean, default: false },
    dashedBorder: { type: Boolean, default: false },
    className: { type: String, default: '' },
    maxlength: { type: Number, default: 255 },
    inputmode: { type: String as PropType<InputType>, default: undefined },
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-alt';
  }
  return 'text-highlight';
});

const inputClasses = computed(() => {
  let classes: string[] = ['px-custom-x py-custom-y transition-colors duration-200 w-full border-none'];

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
    classes.push('outline-1 outline-solid');
  }

  if (props.label === '') {
    classes.push('w-full');
  }

  return classes;
});
</script>

<template>
  <div class="flex flex-row items-center gap-2">
    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
    <div class="relative w-full">
      <input
        :type="type"
        :id="id"
        :placeholder="placeholder"
        :maxlength="maxlength"
        :inputmode="inputmode"
        v-model="modelValue"
        :class="[
          inputClasses,
          className,
        ]"
      />
    </div>
  </div>
</template>
