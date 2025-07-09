<script setup lang="ts">
import Title from '../ui/atoms/Title.vue';
import Input from '../ui/atoms/Input.vue';
import Button from '../ui/atoms/Button.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

let inputValue = ref('');
const router = useRouter();

const redirectToRoom = () => {
    router.push('/room/' + inputValue.value);
};
</script>

<template>
    <section>
        <Title :level="2" class-name="mb-7">Join a Room</Title>
        <div class="flex flex-row flex-nowrap justify-between items-center max-w-xl gap-5">
            <Input id="join-room-input" theme="primary" placeholder="Enter room code" v-model="inputValue" xl/>
            <Button theme="primary" rounded="sm" @click="redirectToRoom"> Join </Button>
        </div>
    </section>
</template>
