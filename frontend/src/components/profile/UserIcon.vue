<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import UserIcon from '@/assets/images/user_icon1.png';

defineEmits(['click']);
</script>

<template>
  <Image :src="UserIcon" alt="user" rounded="full" :width="4" @click="$emit('click')" />
</template>
