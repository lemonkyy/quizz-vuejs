<template>
    <div class="space-y-2">
      <div class="flex gap-4 justify-center w-full mt-6">
        <div class="flex-1 text-center bg-[#f3f1e9] rounded-md py-1.5 font-bold text-xl">
          {{ displayMinutes }}
        </div>
        <div class="flex-1 text-center bg-[#f3f1e9] rounded-md py-1.5 font-bold text-xl">
          {{ displaySeconds }}
        </div>
      </div>
      <div class="flex justify-between text-sm text-[#1c1c1c]/70 px-1">
        <span>Minutes</span>
        <span>Seconds</span>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  
  const props = defineProps({
    duration: Number
  })
  
  const remaining = ref(props.duration ?? 0)
  
  const displayMinutes = computed(() =>
    Math.floor(remaining.value / 60).toString().padStart(2, '0')
  )
  const displaySeconds = computed(() =>
    (remaining.value % 60).toString().padStart(2, '0')
  )
  
  let interval: number
  
  onMounted(() => {
    interval = window.setInterval(() => {
      if (remaining.value > 0) remaining.value--
    }, 1000)
  })
  
  onUnmounted(() => {
    clearInterval(interval)
  })
  </script>
  