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

const isMenuOpen = ref(false);

const auth = useAuthStore();
const userInGame = ref(false); //temporary
const showProfileModal = ref(false);

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
  console.log('isMenuOpen:', isMenuOpen.value);
};

const closeMenu = () => {
  isMenuOpen.value = false;
};

</script>

<template>
    <!-- mobile menu -->
    <ul class="md:hidden flex flex-row flex-nowrap gap-3 items-center ">
      <li v-if="auth.user"><NotificationContainer /></li>
      <li><MenuToggleButton :is-open="isMenuOpen" @toggle="toggleMenu" /></li>
    </ul>
    <!-- normal menu -->
    <ul class="hidden md:flex flex-row justify-around items-center gap-12">
        <ul class="flex flex-row justify-around items-center gap-12" v-if="!userInGame">
            <MenuItem links-to="/">Home</MenuItem>
            <MenuItem links-to="/create" v-if="auth.user">Create</MenuItem>
            <MenuItem links-to="/join" v-if="auth.user">Join</MenuItem>
            <MenuItem links-to="/register" v-if="!auth.user">Register</MenuItem>
            <MenuItem links-to="/login" v-if="!auth.user">Login</MenuItem>
        </ul>
        <li v-else> <HelpButton /> </li>
        <ul v-if="auth.user" class="flex flex-row items-center gap-6">
            <li> <NotificationContainer /> </li>
            <li> <UserIcon class="cursor-pointer" v-on:click="() => {showProfileModal = !showProfileModal}" :src="auth.userProfilePictureUrl" /> </li>
        </ul>
    </ul>
    <MobileMenu :is-open="isMenuOpen" @close-menu="closeMenu" :user-in-game="userInGame" />
    <ProfileModal v-model="showProfileModal" no-header />
</template>