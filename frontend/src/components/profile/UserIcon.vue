<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import UserIconPlaceholder from '@/assets/images/user_icon1.png';

const props = defineProps({
  src: { type: String, default: UserIconPlaceholder },
});

defineEmits(['click']);
</script>

<template>
  <Image :src="props.src" alt="user" rounded="full" :width="4" @click="$emit('click')" />
</template>
