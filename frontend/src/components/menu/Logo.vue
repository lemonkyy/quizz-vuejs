<script setup lang="ts">
import logo from '@/assets/images/icons/logo.svg';
import Image from '../ui/atoms/Image.vue';
</script>

<template>
    <router-link to="/" class="text-menu-text font-bold text-lg flex flex-row flex-nowrap items-center gap-3">
        <Image :src="logo" alt="Logo" />
        <p>QuizUp</p>
    </router-link>

</template>
