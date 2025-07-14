<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import CheckmarkIcon from '@/assets/images/icons/checkmark.svg';
import Image from '../../atoms/Image.vue';

const emit = defineEmits(['click']);
</script>

<template>
  <Button theme="monochrome" size="sm">
    <Image :src="CheckmarkIcon" alt="Accept" :width="1.0" />
  </Button>
</template>
