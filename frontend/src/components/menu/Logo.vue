<script setup lang="ts">
import logo from '@/assets/images/icons/logo.svg';
import Image from '../ui/atoms/Image.vue';
import { useRoomStore } from '@/store/room';

const roomStore = useRoomStore();
</script>

<template>
    <router-link v-if="!roomStore.userInRoom" to="/" class="text-menu-text font-bold text-lg flex flex-row flex-nowrap items-center gap-3">
        <Image :src="logo" alt="Logo" />
        <p>QuizUp</p>
    </router-link>
    <div v-else class="text-menu-text font-bold text-lg flex flex-row flex-nowrap items-center gap-3 cursor-not-allowed">
        <Image :src="logo" alt="Logo" />
        <p>QuizUp</p>
    </div>
</template>
