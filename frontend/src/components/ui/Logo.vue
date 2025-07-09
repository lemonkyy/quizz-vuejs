<script setup lang="ts">
import logo from '@/assets/images/icons/logo.svg';
import Image from './atoms/Image.vue';
</script>

<template>
    <div class="text-menu-text font-bold text-lg flex flex-row flex-nowrap items-center gap-3">
        <Image :src="logo" alt="Logo" />
        <p>QuizUp</p>
    </div>

</template>
