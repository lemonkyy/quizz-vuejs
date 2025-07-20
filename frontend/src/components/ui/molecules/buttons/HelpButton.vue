<script setup lang="ts">
import Button from '../../atoms/Button.vue';
import Help from '@/assets/images/icons/help.svg';
import Image from '../../atoms/Image.vue';
import { useToast } from "vue-toastification";

const props = defineProps({
  tooltipMessage: {
    type: String,
    default: ''
  }
});

const toast = useToast();

const handleClick = () => {
  toast.info(props.tooltipMessage);
};
</script>

<template>
  <Button
    type="button"
    theme="monochrome"
    :title="tooltipMessage ? tooltipMessage : undefined"
    @click="handleClick"
  >
    <Image :src="Help" alt="notification" :width="1.4"/>
  </Button>
</template>
