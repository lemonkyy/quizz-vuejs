<script setup lang="ts">
import { ref, watch } from 'vue';
import Title from './Title.vue';
import Button from './Button.vue';

const modelValue = defineModel({required: true, default: false});

const props = defineProps({
  staticBackdrop: {type: Boolean, default: false},
});

const emit = defineEmits(['update:modelValue']);

const visible = ref(modelValue.value);

watch(
  () => modelValue.value,
  (newVal) => {
    visible.value = newVal;
  }
);

function close() {
  visible.value = false;
  emit('update:modelValue', false);
}

function onBackdropClick() {
  if (!props.staticBackdrop) {
    close();
  }
}

</script>

<template>
  <div
    v-if="visible"
    class="fixed inset-0 z-50 flex justify-center items-center overflow-auto bg-black/40"
    @click.self="onBackdropClick"
  >
    <div class="relative p-4 w-full max-w-2xl max-h-full">
      <div class="relative bg-white rounded-lg shadow-sm">

        <div
          class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200"
        >
          <Title :level=3 v-if="$slots.header">
            <slot name="header" />
          </Title>
          <Button
            withoutBorder
            transparent
            class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 h-10 cursor-pointer"
            @click="close"
            aria-label="Close modal"
          >
            <svg
              class="w-3 h-3"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 14 14"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
              />
            </svg>
          </Button>
        </div>

        <div class="p-4 md:p-5 space-y-4 text-gray-500">
          <slot />
        </div>

        <div
          v-if="$slots.footer"
          class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b"
        >
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>
