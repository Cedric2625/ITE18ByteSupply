export const useLoading = () => {
  const isLoading = ref(false)
  const loadingMessage = ref('Loading...')
  const loadingSubMessage = ref('')

  const showLoading = (message?: string, subMessage?: string) => {
    isLoading.value = true
    loadingMessage.value = message || 'Loading...'
    loadingSubMessage.value = subMessage || ''
  }

  const hideLoading = () => {
    isLoading.value = false
    loadingMessage.value = 'Loading...'
    loadingSubMessage.value = ''
  }

  const withLoading = async <T>(
    fn: () => Promise<T>,
    message?: string,
    subMessage?: string
  ): Promise<T> => {
    try {
      showLoading(message, subMessage)
      const result = await fn()
      return result
    } finally {
      hideLoading()
    }
  }

  return {
    isLoading: readonly(isLoading),
    loadingMessage: readonly(loadingMessage),
    loadingSubMessage: readonly(loadingSubMessage),
    showLoading,
    hideLoading,
    withLoading
  }
}

