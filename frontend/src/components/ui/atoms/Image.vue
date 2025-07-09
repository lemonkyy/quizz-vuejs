<script setup lang="ts">
import { computed, type PropType } from 'vue';

type ImageRadius = 'none' | 'sm' | 'md' | 'lg' | 'full';

const props = defineProps({
  src: {type: String, required: true},
  alt: {type: String, required: true},
  rounded: {type: String as PropType<ImageRadius>, default: 'none'},
  size: {type: [Number] as PropType<number | null>, default: null},
  className: { type: String, default: '' },
});

const imageClasses = computed(() => {
  let classes: string[] = ['max-w-full block h-auto'];

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
    case 'full':
      classes.push('rounded-full');
      break;
    default:
      classes.push('rounded-none');
  }

  if (!props.size) {
    classes.push('w-auto');
  }

  return classes;
});
</script>

<template>
  <img :src="src" :alt="alt" :class="[imageClasses, className]" :style="size ? { width: `${size}rem` } : {}"/>
</template>
