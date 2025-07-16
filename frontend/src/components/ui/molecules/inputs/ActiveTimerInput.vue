<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  minutes: Number,
  seconds: Number
})

const emit = defineEmits(['update:minutes', 'update:seconds'])

const increase = (type: 'minutes' | 'seconds') => {
  const value = Math.min(59, (props[type] ?? 0) + 1)
  emit(`update:${type}`, value)
}

const decrease = (type: 'minutes' | 'seconds') => {
  const value = Math.max(0, (props[type] ?? 0) - 1)
  emit(`update:${type}`, value)
}
</script>


<template>
  <div class="flex gap-4 justify-center">
    <div class="w-24 text-center bg-[#f3f1e9] rounded-md py-3 font-bold text-xl">
      <button type="button" @click.stop.prevent="decrease('minutes')" class="block w-full">−</button>
      {{ (props.minutes ?? 0).toString().padStart(2, '0') }}
      <button type="button" @click.stop.prevent="increase('minutes')" class="block w-full">+</button>
    </div>
    <div class="w-24 text-center bg-[#f3f1e9] rounded-md py-3 font-bold text-xl">
      <button type="button" @click.stop.prevent="decrease('seconds')" class="block w-full">−</button>
      {{ (props.seconds ?? 0).toString().padStart(2, '0') }}
      <button type="button" @click.stop.prevent="increase('seconds')" class="block w-full">+</button>
    </div>
  </div>
</template>
