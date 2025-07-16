<script setup lang="ts">
import CreateRoom from '@/components/home/CreateRoom.vue';
import FeaturedQuizzes from '@/components/home/FeaturedQuizzes.vue';
import JoinRoom from '@/components/home/JoinRoom.vue';
</script>

<template>
  <div class="flex flex-col gap-12 pt-20">
    <FeaturedQuizzes />
    <JoinRoom />
    <CreateRoom />
  </div>
</template>
