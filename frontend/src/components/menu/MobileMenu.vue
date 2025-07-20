<script setup lang="ts">
import { useAuthStore } from '@/store/auth';
import UserIcon from '../profile/UserIcon.vue';
import MenuItem from './MenuItem.vue';
import ProfileModal from '../ui/molecules/modals/ProfileModal.vue';
import CrossButton from '../ui/molecules/buttons/CrossButton.vue';
import { ref } from 'vue';

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['close-menu']);

const auth = useAuthStore();
const showProfileModal = ref(false);

const closeMenu = () => {
  emit('close-menu');
};
</script>

<template>
    <div v-if="isOpen" class="sm:hidden fixed top-0 left-0 w-full h-full bg-menu-background z-50">
        <div class="absolute top-4 right-4">
            <CrossButton @click="closeMenu" />
        </div>
        <ul class="flex flex-col items-center justify-center h-full gap-12 z-50">
            <MenuItem links-to="/" class="text-3xl" @click="closeMenu">Home</MenuItem>
            <MenuItem links-to="/create" v-if="auth.user" class="text-3xl" @click="closeMenu">Create</MenuItem>
            <MenuItem links-to="/public-rooms" v-if="auth.user" class="text-3xl" @click="closeMenu">Join</MenuItem>
            <MenuItem links-to="/register" v-if="!auth.user" class="text-3xl" @click="closeMenu">Register</MenuItem>
            <MenuItem links-to="/login" v-if="!auth.user" class="text-3xl" @click="closeMenu">Login</MenuItem>
            
            <li v-if="auth.user">
                <UserIcon class="cursor-pointer" v-on:click="() => {showProfileModal = !showProfileModal}" :src="auth.userProfilePictureUrl" />
            </li>
        </ul>
    </div>
    <ProfileModal v-model="showProfileModal" no-header />
</template>
