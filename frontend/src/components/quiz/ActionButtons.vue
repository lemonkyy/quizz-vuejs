<template>
    <div class="flex justify-center space-x-4 mt-8">
      <Button theme="primary" type="button" rounded="sm" label="submit" @click="onSubmit"> Submit</Button> 
      <Button type="button" rounded="sm" label="submit" @click="onSkip" style="background-color:#F2F0E8;"> Skip</Button>
    </div>
  </template>
  
  <script setup>
  import Button from '../ui/atoms/Button.vue'
  
  const emit = defineEmits(['submit', 'skip'])
  const onSubmit = () => emit('submit')
  const onSkip = () => emit('skip')
  </script>
  