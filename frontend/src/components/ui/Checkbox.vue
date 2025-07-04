<script setup lang="ts">
import { computed, type PropType } from 'vue';

const modelValue = defineModel<boolean>({required: true, default: false});

type CheckboxStyle = 'primary' | 'secondary';

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    theme: { type: String as PropType<CheckboxStyle>, default: 'primary' },
    className: { type: String, default: '' },
});

const labelClasses = computed(() => {
  if (props.theme === 'secondary') {
    return 'text-purple-800';
  }
  return 'text-blue-800';
});

const checkClasses = computed(() => {
  let classes = [];

  if (props.theme === 'secondary') {
    classes.push('text-purple-800 focus:ring-purple-800');
  } else {
    classes.push('text-blue-800 focus:ring-blue-800');
  }

  return classes;
});
</script>

<template>
  <div class="flex flex-row items-center gap-2">
    <input
      type="checkbox"
      :id="id"
      v-model="modelValue"
      :class="[
        'h-4 w-4 rounded border-gray-300',
        checkClasses,
        className,
      ]"
    />

    <label v-if="label" :for="id" :class="labelClasses">{{ label }}</label>
  </div>
</template>
