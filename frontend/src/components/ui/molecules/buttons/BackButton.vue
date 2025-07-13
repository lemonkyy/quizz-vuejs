<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Back from '@/assets/images/icons/back.svg'
import Image from '../../atoms/Image.vue';
</script>

<template>
  <Button
    withoutBorder
    transparent
    class="hover:bg-modal-close-button-background-hover hover:text-modal-close-button-text-hover cursor-pointer p-1"
    rounded="lg"
    aria-label="Go back"
  >
    <Image :src="Back" alt="back" :width="2" />
  </Button>
</template>
