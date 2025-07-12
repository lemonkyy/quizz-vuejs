<script setup lang="ts">
import Button from '../../atoms/Button.vue';

defineProps({
    loading: {type: Boolean, default: false},
})

defineEmits(['click']);
</script>

<template>
  <Button
    type="button"
    theme="primary"
    rounded="lg"
    @click="$emit('click')"
    :loading="loading"
  >
    Send Friend Request
  </Button>
</template>
