<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Prev from '@/assets/images/icons/left_arrow.svg';
import Image from '../../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="monochrome"
  >
    <Image :src="Prev" alt="notification" :width="1.4"/>
  </Button>
</template>
