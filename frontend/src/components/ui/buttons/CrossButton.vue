<script setup lang="ts">
import Button from '../atoms/Button.vue';
</script>

<template>
  <Button
    withoutBorder
    transparent
    class="hover:bg-modal-close-button-background-hover hover:text-modal-close-button-text-hover h-10 cursor-pointer"
    aria-label="Close modal"
  >
    <svg
      class="w-3 h-3"
      aria-hidden="true"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 14 14"
    >
      <path
        stroke="currentColor"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
      />
    </svg>
  </Button>
</template>
