<script setup lang="ts">
import EditProfileForm from '../../form/EditProfileForm.vue';

const emit = defineEmits(['back']);

const onBack = () => {
  emit('back');
};
</script>

<template>
  <EditProfileForm @back="onBack" />
</template>
