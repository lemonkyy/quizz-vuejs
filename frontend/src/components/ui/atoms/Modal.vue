<script setup lang="ts">
import { ref, watch } from 'vue';
import Title from './Title.vue';
import CrossButton from '../molecules/buttons/CrossButton.vue';
import BackButton from '../molecules/buttons/BackButton.vue';

const modelValue = defineModel({required: true, default: false});

const props = defineProps({
  staticBackdrop: {type: Boolean, default: false},
  bodyClassName: {type: String, default: ''},
  modalClassName: {type: String, default: ''},
  showBackButton: {type: Boolean, default: false}
});

const emit = defineEmits(['update:modelValue', 'back']);

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
    class="fixed inset-0 z-50 flex justify-center items-center overflow-auto bg-modal-backdrop text-black"
    @click.self="onBackdropClick"
  >
    <div :class="['relative p-4 w-full max-w-2xl max-h-full', modalClassName]">
      <div class="relative bg-modal-background rounded-lg shadow-sm h-full">
        <div v-if="showBackButton" class="absolute left-1 p-3">
          <BackButton @click="emit('back')" />
        </div>
        <div v-if="!$slots.header" class="absolute right-1 p-3">
          <CrossButton @click="close" />
        </div>
        <div
          v-else
          class="flex items-center justify-between p-4 border-b rounded-t border-modal-border "
        >
          <Title :level=3>
            <slot name="header" />
          </Title>
          <CrossButton @click="close" />
        </div>

        <div :class="['p-4 md:p-5 space-y-4', bodyClassName]">
          <slot />
        </div>

        <div
          v-if="$slots.footer"
          class="flex items-center p-4 md:p-5 border-t border-modal-border rounded-b"
        >
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>
