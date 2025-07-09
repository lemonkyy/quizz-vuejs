<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '../atoms/Modal.vue';
import Input from '../atoms/Input.vue';

const modelValue = defineModel({required: true, default: false});

const emit = defineEmits(['update:modelValue', 'valid']);

const code = ref('');

watch(code, (newCode) => {
  if (newCode.length === 6) {
    validateCode(newCode);
  }
});

function validateCode(code: string) {
  if (/^\d{6}$/.test(code)) {
    emit('valid', code);
    emit('update:modelValue', false);
  }
}
</script>

<template>
  <Modal v-model="modelValue" :static-backdrop="true">
    <!-- typescript thinks these templates are an error idk why -->
    <!-- @vue-ignore -->
    <template #header>
      Veuillez renseigner votre code OTP
    </template>
    <!-- @vue-ignore -->
    <template #default>
      <Input
        id="login-modal-input"
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
