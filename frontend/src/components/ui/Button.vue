<script setup lang="ts">
import { computed } from 'vue';
import { useRouter } from 'vue-router';

type ButtonType = 'button' | 'submit' | 'reset';


const props = defineProps({
  label: { type: String, default: '' },
  style: { type: String, default: 'neutral' },
  redirectTo: { type: String, default: '' },
  onClick: { type: Function, default: () => {} },
  type: { type: String as () => ButtonType, default: 'button' },
  icon: { type: String, default: '' },
  rightIcon: { type: Boolean, default: false },
  transparent: { type: Boolean, default: false },
  withoutborder: { type: Boolean, default: false },
  dashedborder: { type: Boolean, default: false },
  className: { type: String, default: '' },
});

const router = useRouter();

const buttonClasses = computed(() => {
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
    classes = classes.filter(c => !c.startsWith('bg-') && !c.startsWith('border-'));
    classes.push('bg-transparent');
  }

  return classes;
});

const handleClick = () => {
  if (props.redirectTo) {
    router.push(props.redirectTo);
  } else {
    props.onClick();
  }
};
</script>

<template>
  <button
    :type="type"
    :class="[
      'flex items-center justify-center px-4 py-2 rounded-md font-semibold transition-colors duration-200',
      buttonClasses,
      className,
    ]"
    @click="handleClick"
  >
    <span v-if="icon && !rightIcon" :class="['mr-2', icon]"></span>
    <slot>{{ label }}</slot>
    <span v-if="icon && rightIcon" :class="['ml-2', icon]"></span>
  </button>
</template>
