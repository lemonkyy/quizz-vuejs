<script setup lang="ts">
import { computed, type PropType } from 'vue';
import { useRouter } from 'vue-router';

type ButtonType = 'button' | 'submit' | 'reset';
type ButtonStyle = 'primary' | 'secondary';


const props = defineProps({
  theme: { type: String as PropType<ButtonStyle>, default: 'primary' },
  redirectTo: { type: String, default: '' },
  onClick: { type: Function, default: () => {} },
  type: { type: String as PropType<ButtonType>, default: 'button' },
  icon: { type: String, default: '' },
  rightIcon: { type: Boolean, default: false },
  transparent: { type: Boolean, default: false },
  withoutBorder: { type: Boolean, default: false },
  dashedBorder: { type: Boolean, default: false },
  className: { type: String, default: '' },
});

const router = useRouter();

const buttonClasses = computed(() => {
  let classes = [];

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
      'flex items-center justify-center px-4 py-2 rounded-md font-semibold transition-colors duration-200 cursor-pointer',
      buttonClasses,
      className,
    ]"
    @click="handleClick"
  >
    <span v-if="icon && !rightIcon" :class="['mr-2', icon]"></span>
    <slot></slot>
    <span v-if="icon && rightIcon" :class="['ml-2', icon]"></span>
  </button>
</template>
