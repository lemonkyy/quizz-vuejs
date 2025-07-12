<script setup lang="ts">
import { computed, ref } from 'vue';
import { useAuthStore } from '@/store/auth';
import Button from '@/components/ui/atoms/Button.vue';
import TotpShowSecretModal from '@/components/ui/molecules/modals/TotpShowSecretModal.vue';

const auth = useAuthStore();

const username = computed(() => auth.user?.username || 'N/A');
const email = computed(() => auth.user?.email || 'N/A');
const isLoading = ref(false);

const showTotpModal = ref(false);
const totpSecret = ref<string | null>(null);

const fetchTotpSecret = async () => {
  try {
    isLoading.value = true;
    const response = await auth.generateTotpSecret();
    totpSecret.value = response.totpSecret ?? "";
    showTotpModal.value = true;
  } catch (error) {
  } finally {
    isLoading.value = false;
  }
};

const clearTotpSecret = async () => {
  try {
    isLoading.value = true;
    await auth.clearTotpSecret();
  } catch (error) {
  } finally {
    isLoading.value = false
  }
}

//if user has totp -> button to turn it off, if user doesn't have it -> button to turn it on
const totpToggle = async () => {
  if (auth.user?.hasTotp) {
    clearTotpSecret();
  } else {
    fetchTotpSecret();
  }
}
</script>

<template>
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-4">User Profile</h1>
    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col gap-3">
      <p class="text-lg mb-2"><strong class="font-semibold">Username:</strong> {{ username }}</p>
      <p class="text-lg"><strong class="font-semibold">Email:</strong> {{ email }}</p>

      <Button @click="totpToggle" :loading="isLoading" :disabled="isLoading">
        <span v-if="auth.user?.hasTotp">Désactiver le TOTP</span>
        <span v-else>Ajouter le TOTP</span>
      </Button>
    </div>

    <TotpShowSecretModal v-if="totpSecret" v-model="showTotpModal" :totpSecret="totpSecret" />
  </div>
</template>
