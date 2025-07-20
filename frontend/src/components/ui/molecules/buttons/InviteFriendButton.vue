<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import AddUser from '@/assets/images/icons/add_user.svg';
import Image from '../../atoms/Image.vue';

defineEmits(['click']);
</script>

<template>
  <Button
    type="button"
    theme="monochrome"
    @click="$emit('click')"
  >
    <Image :src="AddUser" alt="Invite friends to your group" :width="1.4"/>
    <span class="ml-2">Invite Friends</span>
  </Button>
</template>
