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
  <div :class="['Create', 'Question'].includes(route.name as string) ? 'flex flex-col min-h-screen' : 'flex flex-col min-h-screen bg-[#f8f6f2]'">
    <Header />
    <main :class="['Create', 'Question'].includes(route.name as string) ? 'flex-grow w-full h-full pt-20' : 'flex-grow px-12 md:px-64 h-full pt-20'">
      <router-view />
    </main>
    <Footer />
  </div>
</template>
