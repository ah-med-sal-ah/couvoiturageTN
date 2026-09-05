import { defineStore } from 'pinia'
import { ref } from 'vue'

/**
 * Cross-cutting UI state: the mobile hidden menu, the create-publication
 * modal, and small placeholder info modals (currently just Help & Support -
 * History became a real page in Part 2). Kept global so any nav entry
 * (bottom nav, sidebar, mobile menu) can trigger them without prop-drilling
 * through the layout tree.
 */
export const useUiStore = defineStore('ui', () => {
  const isMobileMenuOpen = ref(false)
  const isCreatePublicationOpen = ref(false)
  const createPublicationDefaultStatus = ref('passenger')
  const infoModal = ref(null) // 'help' | null
  // Bumped whenever a publication is created, so any open feed (Home,
  // Passenger, Driver) knows to refresh without a direct component link.
  const publicationsVersion = ref(0)

  function openMobileMenu() {
    isMobileMenuOpen.value = true
  }
  function closeMobileMenu() {
    isMobileMenuOpen.value = false
  }

  function openCreatePublication(status = 'passenger') {
    createPublicationDefaultStatus.value = status
    isCreatePublicationOpen.value = true
    closeMobileMenu()
  }
  function closeCreatePublication() {
    isCreatePublicationOpen.value = false
  }

  function openInfoModal(key) {
    infoModal.value = key
    closeMobileMenu()
  }
  function closeInfoModal() {
    infoModal.value = null
  }

  function notifyPublicationCreated() {
    publicationsVersion.value += 1
  }

  return {
    isMobileMenuOpen,
    isCreatePublicationOpen,
    createPublicationDefaultStatus,
    infoModal,
    publicationsVersion,
    openMobileMenu,
    closeMobileMenu,
    openCreatePublication,
    closeCreatePublication,
    openInfoModal,
    closeInfoModal,
    notifyPublicationCreated,
  }
})
