<template>
  <DefaultLayout />
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import DefaultLayout from './layouts/DefaultLayout.vue';

export default defineComponent({
  name: 'App',
  components: {
    DefaultLayout,
  },
});
</script>
