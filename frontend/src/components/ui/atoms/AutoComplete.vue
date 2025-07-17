<script setup lang="ts">
import { ref, watch, type PropType } from 'vue'
import { useIntersectionObserver } from '@/composables/useIntersectionObserver';
import Input from '@/components/ui/atoms/Input.vue';
import LoadingSpinner from '@/components/ui/atoms/LoadingSpinner.vue';

//component for autocomplete
//displays list of string provided by parent component. Emits a load-more event when the user scrolls to the bottom.

const modelValue = defineModel<string>({ default: '' })

const props = defineProps({
  items: { type: Array as PropType<string[]>, required: true},
  hasMore: { type: Boolean, default: false},
  isLoading: { type: Boolean, default: false}
})

const emit = defineEmits<{ (e: 'load-more'): void }>()

const showDropdown = ref(false)
const highlightedIndex = ref(-1)
const loadMoreTrigger = ref<HTMLElement | null>(null) //element that triggers loading more items
const justSelectedItem = ref(false) // to prevent showing on focus after selection
const ignoreNextModelValueWatch = ref(false) //to ignore programmatic modelValue changes

watch(modelValue, (newValue) => {
  if (ignoreNextModelValueWatch.value) {
    ignoreNextModelValueWatch.value = false
    return
  }

  if (newValue.trim()) {
    showDropdown.value = true
  } else {
    showDropdown.value = false
  }
})

//watch the items prop to show the dropdown when results arrive
watch(() => props.items, (newItems) => {
  if (justSelectedItem.value) {
    return;
  }

  if (modelValue.value.trim() && (newItems.length > 0 || props.isLoading)) {
    showDropdown.value = true
  } else {
    showDropdown.value = false
  }
  highlightedIndex.value = -1
})

//show dropdown on focus if there are items
function onFocus() {
  if (justSelectedItem.value) {
    justSelectedItem.value = false
    return
  }
  if (modelValue.value.trim() || props.isLoading) {
    showDropdown.value = true
  }
  highlightedIndex.value = -1
}

function hideDropdown() {
  setTimeout(() => {
    if (!props.isLoading) {
      showDropdown.value = false
    }
  }, 150)
}

function selectItem(item: string) {
  modelValue.value = item
  showDropdown.value = false
  justSelectedItem.value = true
  ignoreNextModelValueWatch.value = true
}

function handleKeyDown(event: KeyboardEvent) {
  if (!showDropdown.value) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      highlightedIndex.value = (highlightedIndex.value + 1) % props.items.length
      break
    case 'ArrowUp':
      event.preventDefault()
      highlightedIndex.value = (highlightedIndex.value - 1 + props.items.length) % props.items.length
      break
    case 'Enter':
      event.preventDefault()
      if (highlightedIndex.value >= 0 && highlightedIndex.value < props.items.length) {
        selectItem(props.items[highlightedIndex.value])
      }
      break
    case 'Escape':
      showDropdown.value = false
      break
  }
}

useIntersectionObserver(loadMoreTrigger, (entries) => {
  if (entries[0].isIntersecting && props.hasMore && !props.isLoading) {
    emit('load-more')
  }
}, { threshold: 0.1 });
</script>

<template>
  <div class="relative w-full" @focusout="hideDropdown">
    <Input
      id="autocomplete-input"
      type="text"
      v-model="modelValue"
      @focus="onFocus"
      @keydown="handleKeyDown"
      placeholder="Type to search..."
      autocomplete="off"
    />

    <ul
      v-if="showDropdown"
      class="absolute z-10 w-full mt-1 bg-autocomplete-background border-autocomplete-border rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <li
        v-for="(item, index) in items"
        :key="index"
        @click="selectItem(item)"
        :class="[
          'px-4 py-2 cursor-pointer hover:bg-autocomplete-item-hover-background',
          {'bg-autocomplete-item-highlight-background': index === highlightedIndex}
        ]"
      >
        {{ item }}
      </li>

      <!-- this element is observed to trigger loading more items when it becomes visible -->
      <li
        v-if="hasMore"
        ref="loadMoreTrigger"
        class="px-4 py-2 text-center text-sm text-autocomplete-loading-text"
      >
        <span v-if="isLoading"><LoadingSpinner color="text-autocomplete-loading-text" /></span>
      </li>
    </ul>
  </div>
</template>


