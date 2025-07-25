import { getCurrentInstance } from 'vue'

export function useMatomo() {
  const instance = getCurrentInstance()
  const $matomo = instance?.appContext.config.globalProperties.$matomo

  const trackEvent = (category: string, action: string, name?: string, value?: number) => {
    if ($matomo && typeof $matomo.trackEvent === 'function') {
      $matomo.trackEvent(category, action, name, value)
    } else {
      console.warn('Matomo trackEvent function is not available or $matomo is undefined.')
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
    } else {
      console.warn('Matomo trackPageView function is not available or $matomo is undefined.')
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
    }  else {
      console.warn('Matomo setUserId function is not available or $matomo is undefined.')
    }
  }

  const setCustomVariable = (index: number, name: string, value: string, scope?: string) => {
    if ($matomo && typeof $matomo.setCustomVariable === 'function') {
      $matomo.setCustomVariable(index, name, value, scope)
    }  else {
      console.warn('Matomo setCustomVariable function is not available or $matomo is undefined.')
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
