<script setup lang="ts">
import Title from '../ui/atoms/Title.vue';
import Input from '../ui/atoms/Input.vue';
import Button from '../ui/atoms/Button.vue';
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useRoomStore } from '@/store/room';
import { useToast } from 'vue-toastification';
import axios from '@/api/axios';
import { useMatomo } from '@/composables/useMatomo';

const inputValue = ref('');
const router = useRouter();
const roomStore = useRoomStore();
const toast = useToast();
const isLoading = ref(false);
const { trackEvent } = useMatomo();

const cleanRoomCode = computed({
  get: () => inputValue.value,
  set: (value: string) => {
    const cleaned = value.replace(/[^a-zA-Z0-9]/g, '');
    inputValue.value = cleaned;
  }
});

const joinRoom = async () => {
    const roomCode = inputValue.value.trim();
    
    if (!roomCode) {
        toast.error('Please enter a room code');
        return;
    }
    
    if (!/^[a-zA-Z0-9]+$/.test(roomCode)) {
        toast.error('Room code can only contain letters and numbers');
        return;
    }
    
    isLoading.value = true;
    
    try {
        const searchResponse = await axios.post('/room/search-by-code', {
            code: roomCode
        });
        
        console.log('Search response:', searchResponse.data);
        
        const responseData = searchResponse.data;
        const roomData = responseData.member;
        
        if (!roomData || !Array.isArray(roomData) || roomData.length < 2) {
            toast.error('Room not found with this code');
            return;
        }
        
        const roomId = roomData[0];
        //const roomCode_returned = roomData[1];
        
        if (!roomId) {
            toast.error('Room not found with this code');
            return;
        }
        
        await roomStore.joinRoom(roomId);
        
        // Track successful room join
        trackEvent('Room', 'Join Success', roomCode, 1);
        
        router.push('/room');
        
    } catch (error: any) {
        console.error('Error joining room:', error);
        
        if (error.response?.status === 404) {
            toast.error('Room not found with this code');
            trackEvent('Room', 'Join Failed', 'Room Not Found', 0);
        } else if (error.response?.status === 400) {
            const errorData = error.response?.data;
            if (errorData?.code === 'ERR_ROOM_FULL') {
                toast.error('Room is full (max 4 players)');
                trackEvent('Room', 'Join Failed', 'Room Full', 0);
            } else {
                toast.error('Cannot join this room');
                trackEvent('Room', 'Join Failed', 'Cannot Join', 0);
            }
        } else {
            toast.error('Error joining room');
            trackEvent('Room', 'Join Failed', 'Unknown Error', 0);
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <section>
        <Title :level="2" class="mb-7">Join a Room</Title>
        <div class="flex flex-row flex-nowrap justify-between items-center max-w-xl gap-5">
            <Input id="join-room-input" theme="primary" placeholder="Enter room code" v-model="cleanRoomCode" xl/>
            <Button theme="primary" rounded="sm" @click="joinRoom" :disabled="isLoading"> 
                {{ isLoading ? 'Joining...' : 'Join' }}
            </Button>
        </div>
    </section>
</template>
