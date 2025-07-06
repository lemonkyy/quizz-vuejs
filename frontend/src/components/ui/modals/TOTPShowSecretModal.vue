<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue';
import Modal from '../atoms/Modal.vue';
import QrCode from 'qrcode.vue';
import { useAuthStore } from '@/store/auth';

const modelValue = defineModel({required: true, default: false});

const props = defineProps({
  totpSecret: { type: String, default: null },
});

const auth = useAuthStore();

const email = computed(() => auth.user?.email || '');
const issuer = 'Quiz-It';

const otpAuthUrl = `otpauth://totp/${issuer}:${email.value}?secret=${props.totpSecret}&issuer=${issuer}&algorithm=SHA1&digits=6&period=30`;

</script>

<template>
  <Modal v-model="modelValue" :static-backdrop="true">
    <!-- typescript thinks these templates are an error idk why -->
    <!-- @vue-ignore -->
    <template #header>
      Votre secret OTP (Scannez le avec votre application d'authentification comme Google Authenticator)
    </template>
    <!-- @vue-ignore -->
    <template #default>
      <div v-if="props.totpSecret" class="flex flex-col gap-3 items-center justify-center">
        <QrCode :value="otpAuthUrl" :size="250" />
        {{ totpSecret }}
      </div>
    </template>
  </Modal>
</template>
