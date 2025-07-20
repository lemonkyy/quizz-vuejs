<script setup lang="ts">
import HelpButton from '../ui/molecules/buttons/HelpButton.vue';
import { useAuthStore } from '@/store/auth';
import NotificationContainer from './notifications/NotificationContainer.vue';
import UserIcon from '../profile/UserIcon.vue';
import MenuItem from './MenuItem.vue';
import ProfileModal from '../ui/molecules/modals/ProfileModal.vue';
import { ref } from 'vue';
import MobileMenu from './MobileMenu.vue';
import MenuToggleButton from '../ui/molecules/buttons/MenuToggleButton.vue';

import { useRoomStore } from '@/store/room';

const isMenuOpen = ref(false);

const auth = useAuthStore();
const roomStore = useRoomStore();
const showProfileModal = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};

const closeMenu = () => {
  isMenuOpen.value = false;
};

</script>

<template>
    <!-- mobile menu -->
    <ul class="sm:hidden flex flex-row flex-nowrap gap-3 items-center" v-if="!roomStore.userInRoom">
      <li v-if="auth.user"><NotificationContainer /></li>
      <li><MenuToggleButton :is-open="isMenuOpen" @toggle="toggleMenu" /></li>
    </ul>
    <!-- normal menu -->
    <ul class="hidden sm:flex flex-row justify-around items-center gap-12" v-if="!roomStore.userInRoom">
        <ul class="flex flex-row justify-around items-center gap-12">
            <MenuItem links-to="/">Home</MenuItem>
            <MenuItem links-to="/create" v-if="auth.user">Create</MenuItem>
            <MenuItem links-to="/public-rooms" v-if="auth.user">Join</MenuItem>
            <MenuItem links-to="/register" v-if="!auth.user">Register</MenuItem>
            <MenuItem links-to="/login" v-if="!auth.user">Login</MenuItem>
        </ul>
        <ul v-if="auth.user && !roomStore.userInRoom" class="flex flex-row items-center gap-6">
            <li> <NotificationContainer /> </li>
            <li> <UserIcon class="cursor-pointer" v-on:click="() => {showProfileModal = !showProfileModal}" :src="auth.userProfilePictureUrl" /> </li>
        </ul>
    </ul>
    <MobileMenu :is-open="isMenuOpen" @close-menu="closeMenu"/>
    <ProfileModal v-model="showProfileModal" no-header v-if="!roomStore.userInRoom" />
    <HelpButton tooltip-message="Menu disabled while in a room." v-else />
</template>
