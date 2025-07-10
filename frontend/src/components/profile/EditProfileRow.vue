<script setup lang="ts">
import Button from '../ui/atoms/Button.vue';
import Paperpen from '@/assets/images/icons/paper_pen.svg'
import Image from '../ui/atoms/Image.vue';
</script>

<template>
  <div class="flex items-center gap-3 rounded-lg hover:bg-gray-100 py-2 sm:px-8 cursor-pointer">
    <Button theme="monochrome" class="p-2">
      <Image :src="Paperpen" alt="user-icon" :width="1.4"/>
    </Button>
    <span class="font-semibold">Edit Profile</span>
  </div>
</template>
