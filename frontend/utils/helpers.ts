// Currency formatting
export const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
}

// Date formatting
export const formatDate = (date: string | Date, format: string = 'short'): string => {
  const d = new Date(date)
  if (format === 'short') {
    return d.toLocaleDateString()
  }
  if (format === 'long') {
    return d.toLocaleString()
  }
  return d.toLocaleDateString()
}

// Relative time (e.g., "2 hours ago")
export const formatRelativeTime = (date: string | Date): string => {
  const now = new Date()
  const then = new Date(date)
  const diffInSeconds = Math.floor((now.getTime() - then.getTime()) / 1000)
  
  if (diffInSeconds < 60) return 'Just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`
  
  return then.toLocaleDateString()
}

// Capitalize first letter
export const capitalize = (str: string): string => {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

// Format date like Laravel's ->format('F j, Y H:i:s')
export const formatDateTime = (date: string | Date): string => {
  const d = new Date(date)
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 
                  'July', 'August', 'September', 'October', 'November', 'December']
  const month = months[d.getMonth()]
  const day = d.getDate()
  const year = d.getFullYear()
  const hours = d.getHours().toString().padStart(2, '0')
  const minutes = d.getMinutes().toString().padStart(2, '0')
  const seconds = d.getSeconds().toString().padStart(2, '0')
  
  return `${month} ${day}, ${year} ${hours}:${minutes}:${seconds}`
}

