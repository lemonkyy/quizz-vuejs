<script setup lang="ts">
import Image from '../ui/atoms/Image.vue';
import Title from '../ui/atoms/Title.vue';

defineProps({
    image: {type: String, required: true},
    title: {type: String, required: true},
    linksTo: {type: String, required: true}
})
</script>

<template>
    <router-link :to="linksTo" class="max-w-lg transition-transform duration-200 ease-in-out hover:-translate-y-2">
        <Image :src="image" alt="quiz image" rounded="sm" :width="20"/>
        <Title :level="5" class="mt-4"> {{ title }} </Title>
    </router-link>
</template>
