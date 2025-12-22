import { ref } from 'vue'

const successMessage = ref('')
const errorMessage = ref('')
const showSuccess = ref(false)
const showError = ref(false)

export const useFlash = () => {
  const flashSuccess = (message: string, duration = 3000) => {
    successMessage.value = message
    showSuccess.value = true
    setTimeout(() => {
      showSuccess.value = false
      successMessage.value = ''
    }, duration)
  }

  const flashError = (message: string, duration = 3000) => {
    errorMessage.value = message
    showError.value = true
    setTimeout(() => {
      showError.value = false
      errorMessage.value = ''
    }, duration)
  }

  const clearFlash = () => {
    showSuccess.value = false
    showError.value = false
    successMessage.value = ''
    errorMessage.value = ''
  }

  return {
    successMessage,
    errorMessage,
    showSuccess,
    showError,
    flashSuccess,
    flashError,
    clearFlash
  }
}

