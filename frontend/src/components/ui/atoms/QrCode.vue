<script setup lang="ts">
import { defineProps, onMounted, type PropType } from 'vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
  value: { type: String, required: true },
  size: { type: Number, default: 200 },
  level: { type: String as PropType<'L' | 'M' | 'Q' | 'H'>, default: 'L' },
  bgColor: { type: String, default: '#ffffff' },
  fgColor: { type: String, default: '#000000' },
});
</script>

<template>
  <div>
    <QrcodeVue
      :value="value"
      :size="size"
      :level="level"
      :bgColor="bgColor"
      :fgColor="fgColor"
    />
  </div>
</template>
