<script setup lang="ts">
import LoadingSpinner from '@/components/ui/atoms/LoadingSpinner.vue';
import FriendListItem from '@/components/profile/FriendListItem.vue';
import { ref, onMounted, watch } from 'vue';
import { useFriendStore } from '@/store/friendship';
import { useIntersectionObserver } from '@/composables/useIntersectionObserver';
import { sendInvitation } from '@/services/invitationService';
import { useToast } from 'vue-toastification';

const friendStore = useFriendStore();
const toast = useToast();

const props = defineProps({
  usernameFilter: { type: String, default: '' },
});

const emit = defineEmits(['update:isLoading']);

const isLoading = ref(false);
const friendsListRef = ref<HTMLElement | null>(null);

const fetchFriends = async (page: number = 1) => {
  isLoading.value = true;
  emit('update:isLoading', true);
  try {
    await friendStore.listFriends(props.usernameFilter, page);
  } finally {
    isLoading.value = false;
    emit('update:isLoading', false);
  }
};

const handleRemoveFriend = async (friendId: string) => {
  await friendStore.removeFriend(friendId);
};

const handleInviteFriend = async (friendId: string) => {
  try {
    await sendInvitation(friendId);
    toast.success('Invitation sent!');
  } catch (error: any) {
    if (error.response && error.response.data.code === 'ERR_INVITATION_ALREADY_SENT') {
      toast.error('Invitation already sent.');
      return;
    }
    console.error('Error sending invitation:', error);
    toast.error('Failed to send invitation.');
  }
};

onMounted(() => {
  fetchFriends();
});

useIntersectionObserver(friendsListRef, (entries) => {
  if (entries[0].isIntersecting && friendStore.hasMoreFriends && !isLoading.value) {
    friendStore.loadMoreFriends();
  }
}, { threshold: 0.1 });

watch(() => props.usernameFilter, () => {
  const debounceTimeout = setTimeout(() => {
    friendStore.friends = [];
    friendStore.hasMoreFriends = true;
    fetchFriends(1);
  }, 300);
  return () => clearTimeout(debounceTimeout);
});
</script>

<template>
  <div v-if="friendStore.friends.length > 0" class="w-full space-y-4">
    <FriendListItem
      v-for="friend in friendStore.friends"
      :key="friend.id"
      :friend="friend"
      @remove-friend="handleRemoveFriend"
      @invite-friend="handleInviteFriend"
    />
    <div ref="friendsListRef" class="text-center py-4">
      <LoadingSpinner v-if="isLoading" color="text-text-secondary" />
      <span v-else-if="friendStore.hasMoreFriends">Scroll to load more...</span>
    </div>
  </div>
  <div v-else-if="!isLoading" class="text-center text-black">
    No friends (｡•́︿•̀｡)
  </div>
</template>
