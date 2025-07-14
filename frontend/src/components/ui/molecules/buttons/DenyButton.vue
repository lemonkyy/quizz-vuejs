<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import DenyIcon from '@/assets/images/icons/deny.svg';
import Image from '../../atoms/Image.vue';


</script>

<template>
  <Button theme="monochrome" size="sm">
    <Image :src="DenyIcon" alt="Deny" :width="1.0" />
  </Button>
</template>
