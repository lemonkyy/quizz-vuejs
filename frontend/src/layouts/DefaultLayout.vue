<script lang="ts">
import { defineComponent } from 'vue';
import { useRoute } from 'vue-router';
import Header from './Header.vue';
import Footer from './Footer.vue';

export default defineComponent({
  name: 'DefaultLayout',
  components: {
    Header,
    Footer,
  },
  setup() {
    const route = useRoute();
    return {
      route
    };
  },
});
</script>

<template>
  <div class="flex flex-col min-h-screen bg-website-background">
    <Header />
    <main class="flex-grow px-12 md:px-64 h-full pt-20">
      <router-view />
    </main>
    <Footer />
  </div>
</template>
