<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, nextTick, defineProps, defineEmits, watch, type PropType } from 'vue'
import Input from '@/components/ui/atoms/Input.vue';

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
const loadMoreTrigger = ref<HTMLElement | null>(null) // Element that triggers loading more items

let observer: IntersectionObserver | null = null

//watch for changes in the input value to show/hide the dropdown
//watch(modelValue, (newValue) => {
//  if (newValue.trim() && props.items.length > 0) {
//    showDropdown.value = true
//  } else {
//    showDropdown.value = false
//  }
//})

//watch the items prop to show the dropdown when results arrive
watch(() => props.items, (newItems) => {
  if (modelValue.value.trim() && newItems.length > 0) {
    showDropdown.value = true
  } else {
    showDropdown.value = false
  }
})

//show dropdown on focus if there are items
function onFocus() {
  if (props.items.length > 0 && modelValue.value.trim()) {
    showDropdown.value = true
  }
}

//hide dropdown when the component loses focus
function hideDropdown() {
  setTimeout(() => {
    showDropdown.value = false
  }, 150)
}

function selectItem(item: string) {
  modelValue.value = item
  showDropdown.value = false
}

//sets up observer to watch the 'loadMoreTrigger' element
function setupObserver() {
  observer = new IntersectionObserver(
    (entries) => {
      // When the trigger element is in view and the component isn't already loading, emit load-more
      if (entries[0].isIntersecting && props.hasMore && !props.isLoading) {
        emit('load-more')
      }
    },
    { threshold: 0.1 }
  )

  //re-observe correft dom element if it is deleted
  watch(() => loadMoreTrigger.value, (newEl, oldEl) => {
    if (oldEl) {
      observer?.unobserve(oldEl);
    }
    if (newEl) {
      observer?.observe(newEl);
    }
  });

  //make sure the element is present in the dom before observing it
  nextTick(() => {
    if (loadMoreTrigger.value) {
      observer?.observe(loadMoreTrigger.value)
    }
  })
}

onMounted(() => {
  setupObserver()
})

onBeforeUnmount(() => {
  if (observer && loadMoreTrigger.value) {
    observer.unobserve(loadMoreTrigger.value)
  }
})
</script>

<template>
  <div class="relative w-full" @focusout="hideDropdown">
    <Input
      id="autocomplete-input"
      type="text"
      v-model="modelValue"
      @focus="onFocus"
      placeholder="Type to search..."
      autocomplete="off"
    />

    <ul
      v-if="showDropdown"
      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
    >
      <li
        v-for="(item, index) in items"
        :key="index"
        @click="selectItem(item)"
        class="px-4 py-2 cursor-pointer hover:bg-gray-100"
      >
        {{ item }}
      </li>

      <!-- this element is observed to trigger loading more items when it becomes visible -->
      <li
        v-if="hasMore"
        ref="loadMoreTrigger"
        class="px-4 py-2 text-center text-sm text-gray-500"
      >
        <span v-if="isLoading">Loading...</span>
      </li>
    </ul>
  </div>
</template>


