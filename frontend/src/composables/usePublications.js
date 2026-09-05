import { computed, reactive, ref, watch } from 'vue'

import { fetchPublications } from '@/api/publications'
import { extractErrorMessage } from '@/lib/http'
import { useUiStore } from '@/stores/ui'

/**
 * Shared feed logic for Home / Passenger / Driver / History: same
 * pagination, filtering and refresh behaviour everywhere. Keeping this in
 * one composable is what lets those views reuse one implementation instead
 * of duplicating fetch/paginate/filter logic.
 *
 * @param {object} options
 * @param {string|null} [options.status] - fixed or initial status filter ('passenger' | 'driver' | null for all).
 * @param {boolean} [options.mine] - scope to the authenticated user's own publications (History).
 */
export function usePublications(options = {}) {
  const { mine = false } = options
  const ui = useUiStore()

  const publications = ref([])
  const loading = ref(false)
  const loadingMore = ref(false)
  const error = ref('')
  const page = ref(1)
  const lastPage = ref(1)
  const status = ref(options.status ?? null)

  const filters = reactive({
    departure_location_id: null,
    arrival_location_id: null,
  })

  const hasMore = computed(() => page.value < lastPage.value)
  const isFiltered = computed(() => Boolean(filters.departure_location_id || filters.arrival_location_id))

  async function load({ append = false } = {}) {
    if (append) {
      loadingMore.value = true
    } else {
      loading.value = true
      page.value = 1
    }
    error.value = ''

    try {
      const { data } = await fetchPublications({
        status: status.value || undefined,
        mine: mine || undefined,
        departure_location_id: filters.departure_location_id || undefined,
        arrival_location_id: filters.arrival_location_id || undefined,
        page: page.value,
      })

      publications.value = append ? [...publications.value, ...data.data] : data.data
      lastPage.value = data.meta.last_page
    } catch (err) {
      error.value = extractErrorMessage(err, 'Could not load publications.')
    } finally {
      loading.value = false
      loadingMore.value = false
    }
  }

  async function loadMore() {
    if (!hasMore.value || loadingMore.value) return
    page.value += 1
    await load({ append: true })
  }

  function applyFilters(newFilters) {
    filters.departure_location_id = newFilters.departure_location_id ?? null
    filters.arrival_location_id = newFilters.arrival_location_id ?? null
    load()
  }

  function clearFilters() {
    filters.departure_location_id = null
    filters.arrival_location_id = null
    load()
  }

  /**
   * Switch the status filter (used by History's All/Driver/Passenger tabs)
   * and reload.
   */
  function setStatus(newStatus) {
    status.value = newStatus || null
    load()
  }

  /**
   * Merge a partial update into one already-loaded publication, e.g. after
   * toggling reservation availability - avoids a full refetch just to
   * reflect a change the caller already knows the result of.
   */
  function patchLocal(id, patch) {
    const index = publications.value.findIndex((p) => p.id === id)
    if (index !== -1) {
      publications.value[index] = { ...publications.value[index], ...patch }
    }
  }

  // Refresh whenever a publication is created anywhere in the app.
  watch(
    () => ui.publicationsVersion,
    () => load(),
  )

  return {
    publications,
    loading,
    loadingMore,
    error,
    hasMore,
    isFiltered,
    filters,
    status,
    load,
    loadMore,
    applyFilters,
    clearFilters,
    setStatus,
    patchLocal,
  }
}
