<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '../../atoms/Modal.vue';
import MenuBody from '@/components/profile/modalBodies/MenuBody.vue';
import FriendsBody from '@/components/profile/modalBodies/FriendsBody.vue';
import AddFriendBody from '@/components/profile/modalBodies/AddFriendBody.vue';
import EditProfileBody from '@/components/profile/modalBodies/EditProfileBody.vue';
import EnableTotpBody from '@/components/profile/modalBodies/EnableTotpBody.vue';
import { useAuthStore } from '@/store/auth';

const auth = useAuthStore();

const props = defineProps({
  initialView: { type: String, default: 'menu' },
});

const modelValue = defineModel({required: true, default: false});
defineEmits(['update:modelValue', 'valid']);

const currentView = ref('menu');

watch(modelValue, (isOpen) => {
  if (isOpen) {
    currentView.value = props.initialView;
  } else {
    currentView.value = 'menu';
  }
});

function showAddFriend() {
  currentView.value = 'addFriend';
}

function showFriends() {
  currentView.value = 'friends'
}

function goBack() {
  if (currentView.value === 'addFriend') {
    currentView.value = 'friends'
  } else {
    currentView.value = 'menu';
  }
}

function showEditProfile() {
  currentView.value = 'editProfile';
}

function toggle2FA() {
  if (!auth.user?.hasTotp) {
    currentView.value = 'show2FA'
  } else {
    auth.clearTotpSecret();
  }
}

</script>

<template>
  <Modal v-model="modelValue" :show-back-button="currentView !== 'menu'" @back="goBack">
    <template #default>
      <div class="grid">
        <MenuBody
          :class="{ invisible: currentView !== 'menu' }"
          class="col-start-1 row-start-1 self-top justify-self-center"
          @show-friend="showFriends"
          @show-profile-editor="showEditProfile"
          @toggle-2fa="toggle2FA"
          @close-modal="modelValue = false"
        />
        <FriendsBody
          v-if="currentView === 'friends'"
          class="col-start-1 row-start-1 self-top mt-10 justify-self-center w-full"
          @show-add-friend="showAddFriend"
        />
        <AddFriendBody
          v-if="currentView === 'addFriend'"
          class="col-start-1 row-start-1 self-top mt-10 justify-self-center w-full"
        />
        <EditProfileBody 
          v-if="currentView === 'editProfile'"
          class="col-start-1 row-start-1 self-top justify-self-center w-full"
          @back="goBack"
        />
        <EnableTotpBody 
          v-if="currentView === 'show2FA'"
          class="col-start-1 row-start-1 self-top justify-self-center w-full"
        />
      </div>
    </template>
  </Modal>  
</template>
*