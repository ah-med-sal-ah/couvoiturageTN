import { defineStore } from 'pinia'
import { ref } from 'vue'

import { fetchLocations } from '@/api/locations'

/**
 * Tunisian locations are a small, mostly-static reference dataset (a few
 * hundred rows), so we fetch them once and cache them for reuse across the
 * departure/arrival selectors and the Home filters.
 */
export const useLocationsStore = defineStore('locations', () => {
  const locations = ref([])
  const loading = ref(false)
  const loaded = ref(false)
  const error = ref(null)

  async function ensureLoaded() {
    if (loaded.value || loading.value) return
    loading.value = true
    error.value = null
    try {
      const { data } = await fetchLocations()
      locations.value = data.data
      loaded.value = true
    } catch {
      error.value = 'Could not load locations.'
    } finally {
      loading.value = false
    }
  }

  return { locations, loading, loaded, error, ensureLoaded }
})
