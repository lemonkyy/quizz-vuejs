<template>
  <div class="bg-gray-100 w-md p-3">
    <h1> Login </h1>
    <form class="flex flex-col gap-5 mt-5">
      <input class="border" type="text" placeholder="Username" />
      <input class="border" type="password" placeholder="Password" />
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'LoginPage',
});
</script>
