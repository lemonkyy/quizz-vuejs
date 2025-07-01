<script lang="ts">
import { defineComponent } from 'vue';
import Menu from './Menu.vue';
import Footer from './Footer.vue';

export default defineComponent({
  name: 'DefaultLayout',
  components: {
    Menu,
    Footer,
  },
});
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <Menu />
    <main class="flex-grow">
      <router-view />
    </main>
    <Footer />
  </div>
</template>
