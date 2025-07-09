<script setup lang="ts">
import Title from '../ui/atoms/Title.vue';
import Button from '../ui/atoms/Button.vue';
</script>

<template>
    <section>
        <Title :level="2" class-name="mb-7">Create Your Own</Title>
        <Button theme="monochrome" rounded="sm"><router-link to="/create-quiz">Create Quiz</router-link> </Button>
    </section>
</template>
