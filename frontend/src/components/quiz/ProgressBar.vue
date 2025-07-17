<template>
    <div class="w-full h-3 bg-[#f3f1e9] rounded-full overflow-hidden">
      <div
        class="h-full bg-yellow-400 transition-all duration-300 ease-in-out"
        :style="{ width: `${progressPercentage}%` }"
      ></div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { computed } from 'vue'
  const props = defineProps<{
    current: number
    total: number
  }>()
  
  const progressPercentage = computed(() => {
    if (props.total === 0) return 0
    return Math.min(100, (props.current / props.total) * 100)
  })
  </script>
  