<script setup lang="ts">
import { computed, ref } from 'vue';
import { useAuthStore } from '@/store/auth';
import Button from '@/components/ui/atoms/Button.vue';
import TOTPShowSecretModal from '@/components/ui/modals/TOTPShowSecretModal.vue';

const auth = useAuthStore();

const username = computed(() => auth.user?.username || 'N/A');
const email = computed(() => auth.user?.email || 'N/A');
const isLoading = ref(false);

const showTOTPModal = ref(false);
const totpSecret = ref<string | null>(null);

const fetchTotpSecret = async () => {
  try {
    isLoading.value = true;
    const response = await auth.generateTotpSecret();
    totpSecret.value = response.TOTPSecret ?? "";
    showTOTPModal.value = true;
  } catch (error) {
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">User Profile</h1>
    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col gap-3">
      <p class="text-lg mb-2"><strong class="font-semibold">Username:</strong> {{ username }}</p>
      <p class="text-lg"><strong class="font-semibold">Email:</strong> {{ email }}</p>

      <Button @click="fetchTotpSecret" :loading="isLoading" :disabled="isLoading">Ajouter le TOTP à votre compte.</Button>
    </div>

    <TOTPShowSecretModal v-if="totpSecret" v-model="showTOTPModal" :totpSecret="totpSecret" />
  </div>
</template>
