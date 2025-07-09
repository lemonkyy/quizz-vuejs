<script setup lang="ts">
import LogoutButton from '@/components/ui/buttons/LogoutButton.vue';
import { useAuthStore } from '@/store/auth';
import { computed } from 'vue';
import NotificationButton from '../ui/buttons/NotificationButton.vue';

const auth = useAuthStore();

const username = computed(() => auth.user?.username || '');
</script>

<template>
    <ul class="text-menu-text text-sm flex flex-row justify-around items-center gap-4 font-bold">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/login">Login</router-link></li>
        <li><router-link to="/register">Register</router-link></li>
        <ul v-if="auth.user" class="flex flex-row gap-2">
            <li> <NotificationButton /> </li>
        </ul>
    </ul>
</template>
