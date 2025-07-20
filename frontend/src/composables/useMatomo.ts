import { inject } from 'vue'

export function useMatomo() {
  const $matomo = inject('$matomo') as any

  const trackEvent = (category: string, action: string, name?: string, value?: number) => {
    if ($matomo && typeof $matomo.trackEvent === 'function') {
      $matomo.trackEvent(category, action, name, value)
    }
  }

  const trackGoal = (goalId: number, customRevenue?: number) => {
    if ($matomo && typeof $matomo.trackGoal === 'function') {
      $matomo.trackGoal(goalId, customRevenue)
    }
  }

  const trackPageView = (customTitle?: string) => {
    if ($matomo && typeof $matomo.trackPageView === 'function') {
      $matomo.trackPageView(customTitle)
    }
  }

  const trackSiteSearch = (keyword: string, category?: string, resultsCount?: number) => {
    if ($matomo && typeof $matomo.trackSiteSearch === 'function') {
      $matomo.trackSiteSearch(keyword, category, resultsCount)
    }
  }

  const setUserId = (userId: string) => {
    if ($matomo && typeof $matomo.setUserId === 'function') {
      $matomo.setUserId(userId)
    }
  }

  const setCustomVariable = (index: number, name: string, value: string, scope?: string) => {
    if ($matomo && typeof $matomo.setCustomVariable === 'function') {
      $matomo.setCustomVariable(index, name, value, scope)
    }
  }

  return {
    trackEvent,
    trackGoal,
    trackPageView,
    trackSiteSearch,
    setUserId,
    setCustomVariable,
  }
}