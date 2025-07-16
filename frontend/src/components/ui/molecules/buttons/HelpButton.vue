<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Help from '@/assets/images/icons/help.svg';
import Image from '../../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="monochrome"
  >
    <Image :src="Help" alt="notification" :width="1.4"/>
  </Button>
</template>
