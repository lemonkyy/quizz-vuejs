<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import QrCode from 'qrcode.vue';
import { useAuthStore } from '@/store/auth';
import LoadingSpinner from '@/components/ui/atoms/LoadingSpinner.vue';
import Title from '@/components/ui/atoms/Title.vue';

const auth = useAuthStore();

const email = computed(() => auth.user?.email || 'N/A');

const isLoading = ref(false);
const totpSecret = ref('');

const issuer = 'QuizUp';

onMounted(async () => {
    try {
        isLoading.value = true;
        const response = await auth.generateTotpSecret();
        totpSecret.value = response.totpSecret ?? "";
    } catch (error) {
    } finally {
        isLoading.value = false;
    }
});

const otpAuthUrl = computed(() => `otpauth://totp/${issuer}:${email.value}?secret=${totpSecret.value}&issuer=${issuer}&algorithm=SHA1&digits=6&period=30`);

</script>

<template>
    <div class="flex flex-col justify-center items-center p-4 gap-3 w-full">
        <Title :level="3" class="mt-2">Your 2FA secret </Title>
        <div v-if="isLoading === false" class="flex flex-col gap-3 items-center justify-center">
            <QrCode :value="otpAuthUrl" :size="250" />
            <span class="text-sm">{{ totpSecret }}</span>
        </div>
        <div v-else>
            <LoadingSpinner />
        </div>
        <span class="font-bold"> How does it work? </span>
        <ul class="flex flex-col justify-center ">
            <li>1. Install an app to manage secrets such as Google Authenticator. </li>
            <li>2. Scan the QR code with the app or directly enter the secret onto it. </li>
            <li>3. 0n each subsequent login, you will be prompted to enter the code shown on the app. These codes last for 30 seconds each. </li>
        </ul>
        <span class="text-red-600">Do not lose this secret. Do not share it with anyone.</span>
    </div>
</template>
