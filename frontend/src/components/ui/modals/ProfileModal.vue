<script setup lang="ts">
import { ref, watch } from 'vue';
import Modal from '../atoms/Modal.vue';
import MenuBody from '@/components/profile/MenuBody.vue';
import AddFriendBody from '@/components/profile/AddFriendBody.vue';
import EditProfileBody from '@/components/profile/EditProfileBody.vue';

const modelValue = defineModel({required: true, default: false});
defineEmits(['update:modelValue', 'valid']);

const currentView = ref('menu');

watch(modelValue, () => {
  currentView.value = 'menu';
});

function showAddFriend() {
  currentView.value = 'addFriend';
}

function showMenu() {
  currentView.value = 'menu';
}

function showEditProfile() {
  currentView.value = 'editProfile';
}
</script>

<template>
  <Modal v-model="modelValue" :show-back-button="currentView !== 'menu'" @back="showMenu">
    <template #default>
      <div class="grid">
        <MenuBody
          :class="{ invisible: currentView !== 'menu' }"
          class="col-start-1 row-start-1 self-top justify-self-center"
          @show-add-friend="showAddFriend"
          @show-profile-editor="showEditProfile"
          @close-modal="modelValue = false"
        />
        <AddFriendBody
          v-if="currentView === 'addFriend'"
          class="col-start-1 row-start-1 self-top mt-10 justify-self-center w-full"
        />
        <EditProfileBody 
          v-if="currentView === 'editProfile'"
          class="col-start-1 row-start-1 self-top justify-self-center w-full"
        />
      </div>
    </template>
  </Modal>  
</template>
*