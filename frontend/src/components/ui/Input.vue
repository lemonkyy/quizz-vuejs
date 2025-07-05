<script setup lang="ts">
import { computed, type PropType } from 'vue';

const modelValue = defineModel({required: true, default: ''});

type CheckboxStyle = 'primary' | 'secondary';

type InputType = 'none' | 'text' | 'tel' | 'url' | 'email' | 'numeric' | 'decimal' | 'search' | undefined;

const props = defineProps({
    id: { type: String },
    label: { type: String, default: '' },
    theme: { type: String as PropType<CheckboxStyle>, default: 'primary' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    withoutBorder: { type: Boolean, default: false },
    dashedBorder: { type: Boolean, default: false },
    className: { type: String, default: '' },
    maxlength: { type: Number, default: 255 },
    inputmode: { type: String as PropType<InputType>, default: undefined }
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-purple-800';
  }
  return 'text-blue-800';
});

const inputClasses = computed(() => {
  let classes = [];

  if (props.theme === 'secondary') {
    classes.push('text-black bg-white outline-purple-800 focus:outline-purple-400');
  } else {
    classes.push('text-black bg-white outline-blue-800 focus:outline-blue-400');
  }

  if (props.withoutBorder) {
    classes.push('outline-none underline');
  } else if (props.dashedBorder) {
    classes.push('outline-2 outline-dashed');
  } else {
    classes.push('outline-2 outline-solid');
  }

  if (props.label === '') {
    classes.push('w-full');
  }

  return classes;
});
</script>

<template>
  <div class="flex flex-row items-center gap-2 max-w-[500px]">
    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
    <input
      :type="type"
      :id="id"
      :placeholder="placeholder"
      :maxlength="maxlength"
      :inputmode="inputmode"
      v-model="modelValue"
      :class="[
        'px-4 py-2 rounded-md transition-colors duration-200',
        inputClasses,
        className,
      ]"
    />
  </div>
</template>