<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Clock from '@/assets/images/icons/clock.svg'
import Image from '../../atoms/Image.vue';
</script>

<template>
  <Button theme="monochrome" class="p-2">
    <Image :src="Clock" alt="quiz-history-icon"/>
  </Button>
</template>
