<script setup lang="ts">
import EditProfileForm from '../form/EditProfileForm.vue';
const emit = defineEmits(['back']);
</script>

<template>
  <EditProfileForm v-on:back="emit('back')" />
</template>
