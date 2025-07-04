<script setup lang="ts">
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';

const emit = defineEmits(['close', 'submit']);
const otp = ref<string>('');

const handleSubmit = () => {
  emit('submit', otp.value);
};
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-80">
      <h2 class="text-xl font-bold mb-4">Authentification à Double-Facteur</h2>
      <form @submit.prevent="handleSubmit" class="flex flex-col gap-4">
        <Input v-model="otp" type="text" placeholder="Enter OTP" />
        <div class="flex justify-end gap-2">
          <Button type="button" @click="$emit('close')" className="w-20">Annuler</Button>
          <Button type="submit" className="w-24">Confirmer</Button>
        </div>
      </form>
    </div>
  </div>
</template>
