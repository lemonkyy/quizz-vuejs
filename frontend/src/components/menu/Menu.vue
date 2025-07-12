<script setup lang="ts">
import HelpButton from '../ui/molecules/buttons/HelpButton.vue';
import { useAuthStore } from '@/store/auth';
import NotificationButton from '../ui/molecules/buttons/NotificationButton.vue';
import UserIcon from '../profile/UserIcon.vue';
import MenuElement from './MenuElement.vue';
import ProfileModal from '../ui/molecules/modals/ProfileModal.vue';
import { ref } from 'vue';

const auth = useAuthStore();
const userInGame = false; //temporary
const showProfileModal = ref(false);

</script>

<template>
    <ul class="flex flex-row justify-around items-center gap-12">
        <ul class="flex flex-row justify-around items-center gap-12" v-if="!userInGame">
            <MenuElement links-to="/">Home</MenuElement>
            <MenuElement links-to="/create" v-if="auth.user">Create</MenuElement>
            <MenuElement links-to="/join" v-if="auth.user">Join</MenuElement>
            <MenuElement links-to="/register" v-if="!auth.user">Register</MenuElement>
            <MenuElement links-to="/login" v-if="!auth.user">Login</MenuElement>
        </ul>
        <li v-else> <HelpButton /> </li>
        <ul v-if="auth.user" class="flex flex-row items-center gap-6">
            <li> <NotificationButton /> </li>
            <li> <UserIcon class="cursor-pointer" v-on:click="() => {showProfileModal = !showProfileModal}" /> </li>
        </ul>
    </ul>
    <ProfileModal v-model="showProfileModal" no-header />
</template>
