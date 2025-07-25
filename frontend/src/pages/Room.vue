<script setup lang="ts">
import { onMounted, ref, watch, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useRoomStore } from '@/store/room';
import { useToast } from 'vue-toastification';
import api from '@/api/axios';
import { useMatomo } from '@/composables/useMatomo';
import Title from '../components/ui/atoms/Title.vue';
import Button from '../components/ui/atoms/Button.vue';
import CountdownTimer from '../components/ui/molecules/inputs/CountdownTimer.vue';
import RoomProfilePictures from '../components/room/RoomProfilePictures.vue';
import InviteFriendButton from '@/components/ui/molecules/buttons/InviteFriendButton.vue';
import ProfileModal from '@/components/ui/molecules/modals/ProfileModal.vue';

const router = useRouter();
const roomStore = useRoomStore();
const toast = useToast();
const { trackEvent } = useMatomo();

const quizReady = ref(false);
const isCheckingQuiz = ref(false);

const quizTopic = ref(localStorage.getItem('currentQuizTopic') || 'Trivia Night');

const timerDuration = ref(15);

const showProfileModal = ref(false);
const profileModalInitialView = ref<'menu' | 'friends'>('menu');

const openProfileModal = (view: 'menu' | 'friends') => {
  profileModalInitialView.value = view;
  showProfileModal.value = true;
};

let refreshInterval: number | null = null;

const checkQuizReadiness = async () => {
  if (!roomStore.currentRoom || isCheckingQuiz.value) {
    return;
  }
  
  isCheckingQuiz.value = true;
  
  try {
    const currentTopic = localStorage.getItem('currentQuizTopic');
    
    if (!currentTopic) {
      isCheckingQuiz.value = false;
      return;
    }
    
    const response = await api.get(`/quizzes?title=${encodeURIComponent(currentTopic.trim().toLowerCase())}`);
    const quizzesArray = response?.data?.['member'] || [];
    
    const readyQuiz = quizzesArray.find((q: any) => 
      typeof q.title === 'string' &&
      q.title.trim().toLowerCase() === currentTopic.trim().toLowerCase() &&
      q.ready === true
    );
    
    quizReady.value = !!readyQuiz;
    
  } catch (error) {
    console.error('Error checking quiz readiness:', error);
  } finally {
    isCheckingQuiz.value = false;
  }
};

const launchQuizForAllPlayers = async (quizId: string) => {
  console.log('Attempting to launch quiz with ID:', quizId);
  try {
    toast.success('Quiz is ready! Starting now...');
    
    router.push({ name: 'Question', params: { id: quizId } });
    
  } catch (error) {
    console.error('Error launching quiz:', error);
    toast.error('Error starting quiz');
  }
};

const startQuiz = async () => {
  const currentTopic = localStorage.getItem('currentQuizTopic');
  
  if (!currentTopic) {
    toast.error('Quiz topic not found');
    return;
  }
  
  try {
    const response = await api.get(`/quizzes?title=${encodeURIComponent(currentTopic.trim().toLowerCase())}`);
    const quizzesArray = response?.data?.['member'] || [];
    
    const readyQuiz = quizzesArray.find((q: any) => 
      typeof q.title === 'string' &&
      q.title.trim().toLowerCase() === currentTopic.trim().toLowerCase() &&
      q.ready === true
    );

    if (readyQuiz) {
      trackEvent('Quiz', 'Start', currentTopic, readyQuiz.id);
      await launchQuizForAllPlayers(readyQuiz.id);
    } else {
      toast.error('Quiz not ready yet');
      trackEvent('Quiz', 'Start Failed', 'Not Ready', 0);
    }
  } catch (error) {
    console.error('Error starting quiz:', error);
    toast.error('Error starting quiz');
    trackEvent('Quiz', 'Start Failed', 'Error', 0);
  }
};

const leaveRoom = async () => {
  try {
    trackEvent('Room', 'Leave', quizTopic.value, 1);
    await roomStore.leaveRoom();
    router.push('/');
  } catch (error) {
    console.error('Error leaving room:', error);
    toast.error('Error leaving room');
    trackEvent('Room', 'Leave Failed', 'Error', 0);
  }
};

const copyRoomCode = async () => {
  if (roomStore.currentRoom?.code) {
    try {
      await navigator.clipboard.writeText(roomStore.currentRoom.code);
      toast.success('Room code copied to clipboard!');
      trackEvent('Room', 'Code Copied', roomStore.currentRoom.code, 1);
    } catch (error) {
      console.error('Failed to copy room code:', error);
      toast.error('Failed to copy room code');
    }
  }
};

onMounted(async () => {

  await roomStore.getCurrentRoom();
  
  console.log('Room state after getCurrentRoom:', roomStore.currentRoom);

  if (!roomStore.currentRoom) {
    router.push('/');
  }

  refreshInterval = window.setInterval(async () => {
    if (roomStore.currentRoom) {
      await roomStore.getCurrentRoom();
      await checkQuizReadiness();
    }
  }, 2000);
});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});

watch(() => roomStore.currentRoom?.roomPlayers, (newPlayers, oldPlayers) => {
  
  if (newPlayers && oldPlayers) {
    const newPlayerCount = newPlayers.length;
    const oldPlayerCount = oldPlayers.length;
    
    if (newPlayerCount > oldPlayerCount) {
      console.log('New player joined the room! Total players:', newPlayerCount);
      console.log('Latest player:', newPlayers[newPlayerCount - 1]);
    } else if (newPlayerCount < oldPlayerCount) {
      console.log('Player left the room! Total players:', newPlayerCount);
    }
  }
}, { deep: true });
</script>

<template>
  <div class="bg-[#f8f6f2] py-8 w-full h-screen">
    <div class="w-full px-8">
      <div v-if="roomStore.currentRoom" class="space-y-8">
        
        <div class="text-center">
          <Title :level="1" class="text-3xl font-bold text-[#2c2c2c] mb-2">
            Quiz Room: {{ quizTopic }}
          </Title>
        </div>

        <div class="space-y-4">
          <div class="flex justify-between items-center flex-wrap">
            <Title :level="2" class="text-xl font-semibold text-[#2c2c2c]">
              Participants ({{ roomStore.currentRoom?.roomPlayers?.length || 0 }})
            </Title>
            <InviteFriendButton @click="openProfileModal('friends')" />
          </div>
          <RoomProfilePictures :players="(roomStore.currentRoom?.roomPlayers || []) as any[]" />
          
          <div class="mt-4 p-3 rounded-lg" :class="quizReady ? 'bg-green-100 border border-green-300' : 'bg-yellow-100 border border-yellow-300'">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 rounded-full" :class="quizReady ? 'bg-green-500' : 'bg-yellow-500'"></div>
              <span class="text-sm font-medium" :class="quizReady ? 'text-green-800' : 'text-yellow-800'">
                {{ quizReady ? 'Quiz Ready!' : 'Preparing quiz...' }}
              </span>
            </div>
            <p class="text-xs mt-1" :class="quizReady ? 'text-green-600' : 'text-yellow-600'">
              {{ quizReady 
                ? (roomStore.currentRoom?.roomPlayers?.length >= 2 
                  ? 'Ready to start!' 
                  : 'Waiting for 2nd player to start') 
                : 'Quiz will start when ready and 2+ players are present' 
              }}
            </p>
            <div v-if="roomStore.currentRoom?.code" class="mt-2 pt-2 border-t" :class="quizReady ? 'border-green-200' : 'border-yellow-200'">
              <p class="text-xs font-medium" :class="quizReady ? 'text-green-700' : 'text-yellow-700'">
                Room Code: 
                <span class="font-mono bg-white px-2 py-1 rounded text-sm cursor-pointer select-all" 
                      :class="quizReady ? 'text-green-800' : 'text-yellow-800'"
                      @click="copyRoomCode">
                  {{ roomStore.currentRoom.code }}
                </span>
              </p>
            </div>
          </div>

          <div class="flex justify-center space-x-4 mt-6">
            <Button 
              v-if="quizReady && roomStore.currentRoom?.roomPlayers?.length >= 2"
              theme="primary" 
              type="button" 
              rounded="sm" 
              @click="startQuiz"
            >
              Start the Quiz
            </Button>
            <Button 
              type="button" 
              rounded="sm" 
              @click="leaveRoom" 
              style="background-color:#F2F0E8;"
            >
              Leave room
            </Button>
          </div>
        </div>

        <div class="space-y-4">
          <Title :level="2" class="text-xl font-semibold text-[#2c2c2c]">
            Countdown
          </Title>
          <div class="mx-auto w-full">
            <CountdownTimer :duration="timerDuration" :showHours="true" />
          </div>
        </div>

      </div>

      <div v-else class="flex justify-center items-center min-h-[400px]">
        <div class="text-center">
          <p class="text-gray-600">Loading room...</p>
        </div>
      </div>
    </div>
  </div>
  <ProfileModal v-model="showProfileModal" :initial-view="profileModalInitialView" />
</template>