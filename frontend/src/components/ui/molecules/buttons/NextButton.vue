<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Next from '@/assets/images/icons/right_arrow.svg';
import Image from '../../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="monochrome"
  >
    <Image :src="Next" alt="notification" :width="1.4"/>
  </Button>
</template>
