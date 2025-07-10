<script setup lang="ts">
import Button from '../atoms/Button.vue';
import Notification from '@/assets/images/icons/notification.svg';
import Image from '../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="monochrome"
  >
    <Image :src="Notification" alt="notification" :width="1.4"/>
  </Button>
</template>
