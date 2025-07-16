import { ref, onMounted, onUnmounted, watch, type Ref } from 'vue';

export function useIntersectionObserver(targetRef: Ref<HTMLElement | null>, callback: IntersectionObserverCallback, options?: IntersectionObserverInit) {
  const observer = ref<IntersectionObserver | null>(null);

  onMounted(() => {
    observer.value = new IntersectionObserver(callback, options);
    if (targetRef.value) {
      observer.value.observe(targetRef.value);
    }
  });

  onUnmounted(() => {
    if (observer.value) {
      observer.value.disconnect();
    }
  });

  watch(targetRef, (newEl, oldEl) => {
    if (observer.value) {
      if (oldEl) {
        observer.value.unobserve(oldEl);
      }
      if (newEl) {
        observer.value.observe(newEl);
      }
    }
  });

  return { observer };
}
