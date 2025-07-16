<template>
    <h2 class="text-2xl font-bold text-center my-6">
      {{ question }}
    </h2>
  </template>
  
  <script setup>
  defineProps({
    question: {
      type: String,
      required: true
    }
  })
  </script>
  