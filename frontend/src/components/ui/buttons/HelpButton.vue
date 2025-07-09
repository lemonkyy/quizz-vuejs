<script setup lang="ts">
import Button from '../atoms/Button.vue';
import Help from '@/assets/images/icons/help.svg';
import Image from '../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="square"
  >
    <Image :src="Help" alt="notification" :size="1.3"/>
  </Button>
</template>
