<script setup lang="ts">
import { computed, type PropType } from 'vue';
import { useRouter } from 'vue-router';

type ButtonType = 'button' | 'submit' | 'reset';
type ButtonStyle = 'primary' | 'secondary';


const props = defineProps({
  theme: { type: String as PropType<ButtonStyle>, default: 'primary' },
  redirectTo: { type: String, default: '' },
  type: { type: String as PropType<ButtonType>, default: 'button' },
  transparent: { type: Boolean, default: false },
  withoutBorder: { type: Boolean, default: false },
  dashedBorder: { type: Boolean, default: false },
  className: { type: String, default: '' },
  loading: { type: Boolean, default: false },
});

const router = useRouter();

const buttonClasses = computed(() => {
  let classes: string[] = [];

  if (props.theme === 'secondary') {
    classes.push('bg-purple-500 text-white hover:bg-purple-600 outline-purple-800');
  } else {
    classes.push('bg-blue-500 text-white hover:bg-blue-600 outline-blue-800');
  }

  if (props.withoutBorder) {
    classes.push('outline-none');
  } else if (props.dashedBorder) {
    classes.push('outline-2 outline-dashed');
  } else {
    classes.push('outline-2 outline-solid');
  }

  if (props.transparent) {
    classes = classes.filter(c => !c.startsWith('bg-') && !c.startsWith('outline-') && !c.startsWith('text-'));
    classes.push('bg-transparent');
    if (props.theme === 'secondary') {
    classes.push('text-purple-500 hover:underline');
  } else {
    classes.push('text-blue-500 hover:underline');
  }
  }

  if (props.loading) {
    classes.push('opacity-50 cursor-not-allowed');
  } else {
    classes.push('cursor-pointer')
  }

  return classes;
});

const handleClick = () => {
  if (props.redirectTo) {
    router.push(props.redirectTo);
  }
};
</script>

<template>
  <button
    :type="type"
    :class="[
      'flex items-center justify-center px-4 py-2 rounded-md font-semibold transition-colors duration-200 max-w-md',
      buttonClasses,
      className,
    ]"
    @click="handleClick"
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
