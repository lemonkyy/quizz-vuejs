<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { searchUsers } from '@/services/userService';
import type { PublicUser } from '@/types';
import AutoComplete from '@/components/ui/atoms/AutoComplete.vue';

const modelValue = defineModel<string>({ default: '' });

const searchedUsers = ref<PublicUser[]>([]);
const hasMore = ref(false);
const isLoading = ref(false);
const currentPage = ref(1);
const items = computed(() => searchedUsers.value.map(u => u.username));

let debounceTimer: number | undefined;

const search = async (query: string, page: number = 1, limit: number = 10) => {
  if (isLoading.value) return;
  isLoading.value = true;

  try {
    const response = await searchUsers(query, page, limit);
    
    if (response.code === 'SUCCESS' && response.users) {
      searchedUsers.value.push(...response.users);
      hasMore.value = response.users.length === limit;
    }

  } catch (error) {
    console.error('Error searching users:', error);
  } finally {
    isLoading.value = false;
  }
};

watch(modelValue, (newValue) => {
  clearTimeout(debounceTimer);
  
  searchedUsers.value = [];
  hasMore.value = false;

  if (newValue.trim()) {
    debounceTimer = setTimeout(() => {
      currentPage.value = 1;
      search(newValue, currentPage.value);
    }, 300);
  }
});

function loadMore() {
  if (hasMore.value) {
    currentPage.value++;
    search(modelValue.value, currentPage.value);
  }
}
</script>

<template>
  <AutoComplete
    v-model="modelValue"
    :items="items"
    :has-more="hasMore"
    :is-loading="isLoading"
    @load-more="loadMore"
  />
</template>
