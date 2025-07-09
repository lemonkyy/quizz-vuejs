<script setup lang="ts">
import { computed, type PropType } from 'vue';

type ButtonType = 'button' | 'submit' | 'reset';
type ButtonStyle = 'primary' | 'secondary';
type ButtonRadius = 'sm' | 'md' | 'lg';

const props = defineProps({
  theme: { type: String as PropType<ButtonStyle>, default: 'primary' },
  rounded: { type: String as PropType<ButtonRadius>, default: 'md' },
  xl: {type: Boolean, default: false},
  type: { type: String as PropType<ButtonType>, default: 'button' },
  transparent: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
  className: { type: String, default: '' },
});

const buttonClasses = computed(() => {
  let classes: string[] = ['flex items-center justify-center px-custom-x py-custom-y font-semibold transition-colors duration-200'];

  if (props.theme === 'secondary') {
    classes.push('bg-button-secondary text-button-secondary-text hover:bg-button-secondary-hover');
  } else {
    classes.push('bg-button-primary text-button-primary-text hover:bg-button-primary-hover');
  }

  if (props.transparent) {
    classes = classes.filter(c => !c.startsWith('bg-') && !c.startsWith('outline-') && !c.startsWith('text-'));
    classes.push('bg-transparent');
    if (props.theme === 'secondary') {
      classes.push('text-button-secondary hover:underline hover:text-button-secondary-focus');
    } else {
      classes.push('text-button-primary hover:underline hover:text-button-primary-focus');
    }
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
  
  if (props.xl) {
    classes.push('max-w-button-xl-width w-full')
  }

  if (props.loading) {
    classes.push('cursor-not-allowed bg-button-loading');
  } else {
    classes.push('cursor-pointer')
  }

  return classes;
});
</script>

<template>
  <button
    :type="type"
    :class="[
      buttonClasses,
      className,
    ]"
    :disabled="loading"
  >
    <slot v-if="loading" name="loading">
      <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </slot>
    <slot v-else />
  </button>
</template>
