<script setup lang="ts">
import Logo from '@/components/menu/Logo.vue';
import Menu from '@/components/menu/Menu.vue';

</script>

<template>
  <nav class="bg-menu-background py-4 px-12 text-white flex flex-row justify-between items-center border border-menu-border border-b-menu-border fixed w-full">
    <Logo />
    <Menu />
  </nav>
</template>
