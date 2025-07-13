<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import QrCodeButton from '../../ui/molecules/buttons/QrCodeButton.vue';

const auth = useAuthStore();
</script>

<template>
  <div class="flex items-center gap-3 rounded-lg hover:bg-gray-100 py-2 sm:px-8 cursor-pointer">
    <QrCodeButton />
    <span v-if="!auth.user?.hasTotp" class="font-semibold">Enable Two-Factor Authentication</span>
    <span v-else class="font-semibold">Disable Two-Factor Authentication</span>
  </div>
</template>
