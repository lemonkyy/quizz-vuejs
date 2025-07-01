<script lang="ts">
import { defineComponent, computed } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'Button',
  props: {
    label: { type: String, default: '' },
    type: { type: String, default: 'neutral' },
    redirectTo: { type: String, default: '' },
    onClick: { type: Function, default: () => {} },
    icon: { type: String, default: '' },
    righticon: { type: Boolean, default: false },
    transparent: { type: Boolean, default: false },
    withoutborder: { type: Boolean, default: false },
    dashedborder: { type: Boolean, default: false },
    className: { type: String, default: '' },
  },

  setup(props) {
    const router = useRouter();

    const buttonClasses = computed(() => {
      let classes = [];

      if (props.type === 'secondary') {
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
        classes = classes.filter(c => !c.startsWith('bg-'||'border-'));
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

    return {
      buttonClasses,
      handleClick,
    };

  },
});
</script>

<template>
  <button
    :type="buttonType"
    :class="[
      'flex items-center justify-center px-4 py-2 rounded-md font-semibold transition-colors duration-200',
      buttonClasses,
      className,
    ]"
    @click="handleClick"
  >
    <span v-if="icon && !righticon" :class="['mr-2', icon]"></span>
    <slot>{{ label }}</slot>
    <span v-if="icon && righticon" :class="['ml-2', icon]"></span>
  </button>
</template>