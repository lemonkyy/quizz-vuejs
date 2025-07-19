<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useRoomStore } from '@/store/room';
//import { useToast } from 'vue-toastification';
import Title from '../components/ui/atoms/Title.vue';
import Button from '../components/ui/atoms/Button.vue';

const router = useRouter();
const roomStore = useRoomStore();
//const toast = useToast();

const quizTopic = ref(localStorage.getItem('currentQuizTopic') || 'Trivia Night');

const results = ref([
  { rank: 1, username: 'admin', score: 8 },
  { rank: 2, username: 'user2', score: 6 }
]);

const leaveRoom = async () => {
  try {
    if (roomStore.currentRoom) {
      await roomStore.leaveRoom();
    }
    router.push('/');
  } catch (error) {
    console.error('Error leaving room:', error);
    router.push('/');
  }
};

onMounted(async () => {
  if (!roomStore.currentRoom) {
    await roomStore.getCurrentRoom();
  }
  if (!roomStore.currentRoom && !quizTopic.value) {
    router.push('/');
  }
});
</script>

<template>
  <div class="min-h-screen bg-[#f8f6f2] py-8 w-full">
    <div class="w-full px-8">
      <div class="space-y-8">
        <div class="text-center">
          <Title :level="1" class="text-3xl font-bold text-[#2c2c2c] mb-2">
            Live Quiz Results
          </Title>
          <Title :level="2" class="text-xl text-[#666] mb-6">
            Quiz Room: {{ quizTopic }}
          </Title>
        </div>
        <div class="w-full">
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-[#2c2c2c] text-white px-6 py-4">
              <div class="grid grid-cols-3 gap-4 font-semibold">
                <div class="text-center">Rank</div>
                <div class="text-center">Username</div>
                <div class="text-center">Score</div>
              </div>
            </div>

            <div class="divide-y divide-gray-200">
              <div 
                v-for="result in results" 
                :key="result.username"
                class="px-6 py-4 hover:bg-gray-50 transition-colors"
              >
                <div class="grid grid-cols-3 gap-4 items-center">
                  <div class="text-center font-bold text-lg">
                    {{ result.rank }}
                  </div>
                  <div class="text-center font-medium">
                    {{ result.username }}
                  </div>
                  <div class="text-center font-semibold text-lg">
                    {{ result.score }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-center mt-8">
          <Button 
            type="button" 
            theme="primary"
            rounded="sm" 
            @click="leaveRoom"
          >
            Next
          </Button>
        </div>

      </div>
    </div>
  </div>
</template>