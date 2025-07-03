<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    level: { type: Number, default: 1 },
    className: { type: String, default: '' },
});

const tag = computed(() => `h${props.level}`);

const titleClasses = computed(() => {
  let classes = ['font-bold'];
  
  switch (props.level) {
    case 1:
      classes.push('text-4xl');
      break;
    case 2:
      classes.push('text-3xl');
      break;
    case 3:
      classes.push('text-2xl');
      break;
    case 4:
      classes.push('text-xl');
      break;
    default:
      classes.push('text-lg');
      break;
  }

  return classes;
});
</script>

<template>
  <component :is="tag" :class="[titleClasses, className]">
    <slot></slot>
  </component>
</template>
