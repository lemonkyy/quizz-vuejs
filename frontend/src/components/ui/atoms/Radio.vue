<script setup lang="ts">
const modelValue = defineModel<string | number>({required: true});

defineProps({
    id: { type: String, required: true },
    value: { type: [String, Number], required: true },
    label: { type: String, default: '' },
    className: { type: String, default: '' },
    radioLeft: { type: Boolean, default: false },
    name: { type: String, required: true },
});
</script>

<template>
  <div class="flex items-center gap-5" :class="[radioLeft ? 'flex-row' : 'flex-row-reverse']">
    <input
      type="radio"
      :id="id"
      :name="name"
      :value="value"
      v-model="modelValue"
      :class="[
        'h-5 w-5 rounded-full border-alt', 'text-primary focus:ring-primary cursor-pointer',
        className,
      ]"
    />

    <label v-if="label" :for="id" class="text-black cursor-pointer">{{ label }}</label>
  </div>
</template>
