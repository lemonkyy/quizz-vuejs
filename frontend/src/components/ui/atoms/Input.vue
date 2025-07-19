<script setup lang="ts">
import { computed, type PropType } from 'vue';

const modelValue = defineModel<string | number>({
  default: ''
});

type InputType = 'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search' | undefined;
type InputStyle = 'primary' | 'secondary';
type ButtonRadius = 'sm' | 'md' | 'lg';

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    theme: { type: String as PropType<InputStyle>, default: 'primary' },
    rounded: { type: String as PropType<ButtonRadius>, default: 'md' },
    xl: {type: Boolean, default: false},
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    withoutBorder: { type: Boolean, default: false },
    dashedBorder: { type: Boolean, default: false },
    className: { type: String, default: '' },
    maxlength: { type: Number, default: 255 },
    inputmode: { type: String as PropType<InputType>, default: undefined },
    autocomplete: { type: String, default: undefined },
    
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-input-second-text';
  }
  return 'text-input-primary-text';
});

const parentClasses = computed(() => {
  if (props.xl || props.label === '') {
    return 'w-full';
  }
})

const inputClasses = computed(() => {
  let classes: string[] = ['p-4 transition-colors duration-200 border-none focus:ring-0 text-lg'];

  if (props.theme === 'secondary') {
    classes.push('text-input-secondary-text placeholder-input-secondary-placeholder bg-input-secondary-background outline-input-secondary-outline focus:outline-secondary-outline-focus');
  }else{
    classes.push('text-input-primary-text placeholder-input-primary-placeholder bg-input-primary-background outline-input-primary-outline focus:outline-input-primary-outline-focus');
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

  if (props.xl) {
    classes.push('max-w-custom-xl w-full')
  }

  return classes;
});
</script>

<template>
  <div :class="['flex flex-row items-center gap-2', parentClasses]">
    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
      <input
        :type="type"
        :id="id"
        :placeholder="placeholder"
        :maxlength="maxlength"
        :inputmode="inputmode"
        :autocomplete="autocomplete"
        v-model="modelValue"
        :class="[
          inputClasses,
          className,
        ]"
      />
  </div>
</template>
