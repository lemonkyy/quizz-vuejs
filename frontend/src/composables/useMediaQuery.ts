import { ref, onMounted, onUnmounted } from 'vue';

export function useMediaQuery(query: string) {
  const matches = ref(false);

  const mediaQueryList = typeof window !== 'undefined' ? window.matchMedia(query) : undefined;

  const updateMatches = () => {
    if (mediaQueryList) {
      matches.value = mediaQueryList.matches;
    }
  };

  onMounted(() => {
    if (mediaQueryList) {
      updateMatches();
      mediaQueryList.addEventListener('change', updateMatches);
    }
  });

  onUnmounted(() => {
    if (mediaQueryList) {
      mediaQueryList.removeEventListener('change', updateMatches);
    }
  });

  return matches;
}
