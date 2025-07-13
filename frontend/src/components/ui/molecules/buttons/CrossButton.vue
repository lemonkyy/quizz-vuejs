<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Cross from '@/assets/images/icons/cross.svg';
import Image from '../../atoms/Image.vue';
</script>

<template>
  <Button
    withoutBorder
    transparent
    class="hover:bg-modal-close-button-background-hover hover:text-modal-close-button-text-hover cursor-pointer p-1"
    rounded="lg"
    aria-label="Close modal"
  >
    <Image :src="Cross" alt="cross" :width="2" />
  </Button>
</template>
