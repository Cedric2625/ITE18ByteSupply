<template>
  <div v-if="totalPages > 1" class="flex justify-center items-center space-x-1 mt-6">
    <!-- Previous Button -->
    <button
      @click="goToPage(currentPage - 1)"
      :disabled="currentPage === 1"
      class="px-3 py-2 rounded-md bg-gray-700 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-600 transition-colors"
      :class="{ 'cursor-not-allowed': currentPage === 1 }"
    >
      <i class="fas fa-chevron-left"></i>
    </button>

    <!-- First Page -->
    <button
      v-if="showFirstPage"
      @click="goToPage(1)"
      :class="[
        'px-4 py-2 rounded-md transition-colors',
        currentPage === 1
          ? 'bg-blue-500 text-white'
          : 'bg-gray-700 text-white hover:bg-gray-600'
      ]"
    >
      1
    </button>

    <!-- First Ellipsis -->
    <span v-if="showFirstEllipsis" class="px-2 text-gray-500">...</span>

    <!-- Page Numbers -->
    <button
      v-for="page in visiblePages"
      :key="page"
      @click="goToPage(page)"
      :class="[
        'px-4 py-2 rounded-md transition-colors',
        currentPage === page
          ? 'bg-blue-500 text-white'
          : 'bg-gray-700 text-white hover:bg-gray-600'
      ]"
    >
      {{ page }}
    </button>

    <!-- Last Ellipsis -->
    <span v-if="showLastEllipsis" class="px-2 text-gray-500">...</span>

    <!-- Last Page -->
    <button
      v-if="showLastPage"
      @click="goToPage(totalPages)"
      :class="[
        'px-4 py-2 rounded-md transition-colors',
        currentPage === totalPages
          ? 'bg-blue-500 text-white'
          : 'bg-gray-700 text-white hover:bg-gray-600'
      ]"
    >
      {{ totalPages }}
    </button>

    <!-- Next Button -->
    <button
      @click="goToPage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="px-3 py-2 rounded-md bg-gray-700 text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-600 transition-colors"
      :class="{ 'cursor-not-allowed': currentPage === totalPages }"
    >
      <i class="fas fa-chevron-right"></i>
    </button>
  </div>
</template>

<script setup lang="ts">
interface Props {
  currentPage: number
  totalPages: number
  maxVisiblePages?: number
}

const props = withDefaults(defineProps<Props>(), {
  maxVisiblePages: 7
})

const emit = defineEmits<{
  'page-change': [page: number]
}>()

const visiblePages = computed(() => {
  const pages: number[] = []
  const { currentPage, totalPages, maxVisiblePages } = props

  if (totalPages <= maxVisiblePages) {
    // Show all pages if total is less than max visible
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i)
    }
  } else {
    // Calculate range around current page
    const halfVisible = Math.floor((maxVisiblePages - 2) / 2) // -2 for first and last
    let start = currentPage - halfVisible
    let end = currentPage + halfVisible

    // Adjust start and end to stay within bounds
    if (start < 2) {
      start = 2
      end = Math.min(start + maxVisiblePages - 3, totalPages - 1)
    }
    if (end > totalPages - 1) {
      end = totalPages - 1
      start = Math.max(end - maxVisiblePages + 3, 2)
    }

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
  }

  return pages
})

const showFirstPage = computed(() => {
  return props.totalPages > props.maxVisiblePages && !visiblePages.value.includes(1)
})

const showLastPage = computed(() => {
  return props.totalPages > props.maxVisiblePages && !visiblePages.value.includes(props.totalPages)
})

const showFirstEllipsis = computed(() => {
  return showFirstPage.value && visiblePages.value[0] > 2
})

const showLastEllipsis = computed(() => {
  return showLastPage.value && visiblePages.value[visiblePages.value.length - 1] < props.totalPages - 1
})

const goToPage = (page: number) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('page-change', page)
  }
}
</script>

