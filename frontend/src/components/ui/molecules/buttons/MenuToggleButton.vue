<script setup lang="ts">
import Image from '@/components/ui/atoms/Image.vue';
import BurgerIcon from '@/assets/images/icons/burger.svg';
import CrossButton from '@/components/ui/molecules/buttons/CrossButton.vue';

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
});

const emit = defineEmits(['toggle']);

const toggle = () => {
  emit('toggle');
};
</script>

<template>
  <div class="md:hidden relative">
    <Image v-if="!isOpen" :src="BurgerIcon" alt="Menu" class="w-8 h-8 cursor-pointer" @click="toggle" :width="4" />
    <CrossButton v-else @click="toggle" class="absolute right-4" />
  </div>
</template>
