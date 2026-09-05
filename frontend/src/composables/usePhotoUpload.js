import { reactive } from 'vue'
import { useI18n } from 'vue-i18n'

// Keep in sync with the backend's `image` + `max:8192` rule (RegisterRequest /
// UpdateProfileRequest) so a bad file is rejected instantly instead of only
// after a round trip to the server.
const MAX_SIZE_BYTES = 8 * 1024 * 1024

/**
 * Shared profile-photo selection logic for the Register and Profile forms:
 * validates type/size up front, builds a preview URL, and exposes a single
 * `error` string the template can render - this is what was missing before,
 * silently leaving a rejected photo (wrong format, too large) with no
 * visible explanation beyond a generic "invalid data" banner.
 *
 * Returned as a single reactive object (state + methods) so both templates
 * and script can read `photo.file` / `photo.preview` / `photo.error`
 * directly, with no `.value` unwrapping to get wrong.
 */
export function usePhotoUpload() {
  const { t } = useI18n()

  const photo = reactive({
    file: null,
    preview: '',
    error: '',

    select(event) {
      const selected = event.target.files?.[0]
      if (!selected) return

      photo.error = ''

      if (!selected.type.startsWith('image/')) {
        photo.error = t('auth.register.photoInvalidType')
        event.target.value = ''
        return
      }

      if (selected.size > MAX_SIZE_BYTES) {
        photo.error = t('auth.register.photoTooLarge')
        event.target.value = ''
        return
      }

      photo.file = selected
      photo.preview = URL.createObjectURL(selected)
    },

    clear(inputEl) {
      photo.file = null
      photo.preview = ''
      photo.error = ''
      if (inputEl) inputEl.value = ''
    },
  })

  return photo
}
