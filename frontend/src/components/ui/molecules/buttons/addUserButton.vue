<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import AddUser from '@/assets/images/icons/add_user.svg';
import Image from '../../atoms/Image.vue';

</script>

<template>
  <Button
    type="button"
    theme="monochrome"
  >
    <Image :src="AddUser" alt="notification" :width="1.4"/>
  </Button>
</template>
