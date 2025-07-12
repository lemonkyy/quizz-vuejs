<script setup lang="ts">
import { computed, type PropType } from 'vue';
import LoadingSpinner from '@/components/ui/atoms/LoadingSpinner.vue';

type ButtonType = 'button' | 'submit' | 'reset';
type ButtonStyle = 'primary' | 'secondary' | 'monochrome';
type ButtonRadius = 'sm' | 'md' | 'lg';

const props = defineProps({
  theme: { type: String as PropType<ButtonStyle>, default: 'primary' },
  rounded: { type: String as PropType<ButtonRadius>, default: 'md' },
  xl: {type: Boolean, default: false},
  type: { type: String as PropType<ButtonType>, default: 'button' },
  transparent: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(['click']);

const buttonClasses = computed(() => {
  let classes: string[] = ['font-semibold transition-colors duration-200 h-auto flex flex-row justify-center items-center'];

  if (props.theme === 'secondary') {
    classes.push('bg-button-secondary text-button-secondary-text hover:bg-button-secondary-hover px-7 py-3');
  }else if (props.theme === 'monochrome'){
    classes.push('bg-button-monochrome text-button-monochrome-text hover:bg-button-monochrome-hover p-3');
  } else {
    classes.push('bg-button-primary text-button-primary-text hover:bg-button-primary-hover px-7 py-3');
  }

  if (props.transparent) {
    classes = classes.filter(c => !c.startsWith('bg-') && !c.startsWith('outline-') && !c.startsWith('text-'));
    classes.push('bg-transparent text-button-transparent-text hover:underline hover:text-button-secondary-focus');
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
    classes.push('max-w-custom-xl w-full')
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
    :class="buttonClasses"
    :disabled="loading"
    @click="emit('click')"
  >
    <slot v-if="loading" name="loading">
      <LoadingSpinner />
    </slot>
    <slot v-else />
  </button>
</template>
