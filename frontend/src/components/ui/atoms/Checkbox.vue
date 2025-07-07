<script setup lang="ts">
const modelValue = defineModel<boolean>({required: true, default: false});

defineProps({
    id: { type: String, required: true },
    label: { type: String, default: '' },
    className: { type: String, default: '' },
    checkboxLeft: { type: Boolean, default: false },
});
</script>

<template>
  <div class="flex items-center gap-5" :class="[checkboxLeft ? 'flex-row' : 'flex-row-reverse']">
    <input
      type="checkbox"
      :id="id"
      v-model="modelValue"
      :class="[
        'h-5 w-5 rounded border-alt', 'text-primary focus:ring-primary cursor-pointer',
        className,
      ]"
    />

    <label v-if="label" :for="id" class="text-black cursor-pointer">{{ label }}</label>
  </div>
</template>
