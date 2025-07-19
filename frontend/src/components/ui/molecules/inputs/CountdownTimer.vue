<template>
  <div class="space-y-2 w-full">
    <div class="flex gap-4 justify-center w-full mt-6">
      <template v-if="showHours">
        <div class="flex-1 text-center bg-[#f3f1e9] rounded-md py-1.5 font-bold text-xl">
          {{ displayHours }}
        </div>
      </template>
      <div class="flex-1 text-center bg-[#f3f1e9] rounded-md py-1.5 font-bold text-xl">
        {{ displayMinutes }}
      </div>
      <div class="flex-1 text-center bg-[#f3f1e9] rounded-md py-1.5 font-bold text-xl">
        {{ displaySeconds }}
      </div>
    </div>
    <div class="flex gap-4 text-sm text-[#1c1c1c]/70 px-1">
      <template v-if="showHours">
        <span class="flex-1 text-center">Hours</span>
      </template>
      <span class="flex-1 text-center">Minutes</span>
      <span class="flex-1 text-center">Seconds</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  duration: Number,
  showHours: {
    type: Boolean,
    default: false
  },
  isActive: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['timeExpired'])

const remaining = ref(props.duration ?? 0)

const displayHours = computed(() =>
  Math.floor(remaining.value / 3600).toString().padStart(2, '0')
)

const displayMinutes = computed(() => {
  if (props.showHours) {
    return Math.floor((remaining.value % 3600) / 60).toString().padStart(2, '0')
  } else {
    return Math.floor(remaining.value / 60).toString().padStart(2, '0')
  }
})

const displaySeconds = computed(() =>
  (remaining.value % 60).toString().padStart(2, '0')
)

let interval: number

onMounted(() => {
  interval = window.setInterval(() => {
    if (props.isActive && remaining.value > 0) {
      remaining.value--
      if (remaining.value === 0) {
        emit('timeExpired')
      }
    }
  }, 1000)
})

watch(() => props.duration, (newDuration) => {
  remaining.value = newDuration ?? 0
})

onUnmounted(() => {
  clearInterval(interval)
})
</script>
