<template>
    <div class="flex justify-center space-x-4 mt-8">
      <Button
        label="Submit"
        type="secondary"
        @click="onSubmit"
      />
      <Button
        label="Skip"
        :transparent="true"
        @click="onSkip"
      />
    </div>
  </template>
  
  <script setup>
  import Button from '../ui/Button.vue'
  
  const emit = defineEmits(['submit', 'skip'])
  const onSubmit = () => emit('submit')
  const onSkip = () => emit('skip')
  </script>
  