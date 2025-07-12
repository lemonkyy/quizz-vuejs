<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Paperpen from '@/assets/images/icons/paper_pen.svg'
import Image from '../../atoms/Image.vue';
</script>

<template>
  <Button theme="monochrome" class="p-2">
    <Image :src="Paperpen" alt="edit-profile-icon" :width="1.4"/>
  </Button>
</template>
