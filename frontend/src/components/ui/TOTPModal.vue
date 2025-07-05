<script setup>
import { ref, watch } from 'vue';
import Modal from './Modal.vue';
import Input from './Input.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false }
});
const emit = defineEmits(['update:modelValue', 'valid']);

const code = ref('');
const error = ref(null);

watch(code, (newCode) => {
  if (newCode.length === 6) {
    validateCode(newCode);
  } else {
    error.value = null;
  }
});

function validateCode(code) {
  if (/^\d{6}$/.test(code)) {
    error.value = null;
    emit('valid', code);
    emit('update:modelValue', false);
  } else {
    error.value = 'Invalid code format';
  }
}

function close() {
  emit('update:modelValue', false);
  code.value = '';
  error.value = null;
}
</script>

<template>
  <Modal :model-value="modelValue" @update:modelValue="emit('update:modelValue', $event)" title="Enter TOTP Code" :static-backdrop="true">
    <template #header>
      Veuillez renseigner votre code TOTP
    </template>
    <template #default>
      <Input
        type="text"
        inputmode="numeric"
        :maxlength=6
        placeholder="111111"
        v-model="code"
        className=""
      />
    </template>
  </Modal>
</template>
