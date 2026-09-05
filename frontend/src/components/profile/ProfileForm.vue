<script setup>
import { reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

import AppIcon from '@/components/common/AppIcon.vue'
import { usePhotoUpload } from '@/composables/usePhotoUpload'
import { useUsernameCheck } from '@/composables/useUsernameCheck'
import { extractErrorMessage, isGloballyHandled } from '@/lib/http'
import { useAuthStore } from '@/stores/auth'
import { useNotificationStore } from '@/stores/notifications'

const { t } = useI18n()
const auth = useAuthStore()
const notifications = useNotificationStore()

const form = reactive({
  first_name: '',
  last_name: '',
  cin: '',
  age: '',
  username: '',
  gender: '',
})

const photo = usePhotoUpload()
const usernameCheck = useUsernameCheck()
const removePhotoFlag = ref(false)
const errors = ref({})
const saving = ref(false)
const fileInput = ref(null)

function syncFromUser(user) {
  if (!user) return
  form.first_name = user.first_name
  form.last_name = user.last_name
  form.cin = user.cin
  form.age = user.age
  form.username = user.username
  form.gender = user.gender
  photo.clear()
  removePhotoFlag.value = false
}

watch(() => auth.user, syncFromUser, { immediate: true })

function fieldError(field) {
  if (field === 'profile_photo') {
    return errors.value?.profile_photo?.[0] || photo.error || ''
  }
  return errors.value?.[field]?.[0] || ''
}

function onPhotoChange(event) {
  photo.select(event)
  if (photo.file) removePhotoFlag.value = false
}

function removePhoto() {
  photo.clear(fileInput.value)
  removePhotoFlag.value = true
}

function onUsernameBlur() {
  // Skip the check if it's still the user's own current username - it's
  // "taken" by definition (by them), which isn't a useful warning here.
  if (form.username === auth.user?.username) {
    usernameCheck.reset()
    return
  }
  usernameCheck.check(form.username)
}

function useSuggestedUsername() {
  form.username = usernameCheck.suggestion
  usernameCheck.reset()
}

async function submit() {
  errors.value = {}
  saving.value = true
  try {
    const payload = { ...form }
    if (photo.file) payload.profile_photo = photo.file
    if (removePhotoFlag.value) payload.remove_profile_photo = 1
    await auth.updateProfile(payload)
    notifications.success(t('profile.updated'))
  } catch (error) {
    if (error?.response?.status === 422) {
      errors.value = error.response.data.errors || {}
    } else if (!isGloballyHandled(error)) {
      notifications.error(extractErrorMessage(error))
    }
  } finally {
    saving.value = false
  }
}

const displayedPhoto = () => photo.preview || (removePhotoFlag.value ? '' : auth.user?.profile_photo_url) || ''
</script>

<template>
  <form class="space-y-5" @submit.prevent="submit">
    <div>
      <div class="flex items-center gap-4">
        <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-slate-400">
          <img v-if="displayedPhoto()" :src="displayedPhoto()" alt="" class="h-full w-full object-cover" />
          <AppIcon v-else name="profile" :size="28" />
        </div>
        <div class="flex gap-2">
          <label class="btn-secondary cursor-pointer text-xs">
            {{ t('profile.changePhoto') }}
            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
          </label>
          <button v-if="displayedPhoto()" type="button" class="btn-danger-ghost text-xs" @click="removePhoto">
            {{ t('auth.register.removePhoto') }}
          </button>
        </div>
      </div>
      <p v-if="fieldError('profile_photo')" class="form-error">{{ fieldError('profile_photo') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="form-label">{{ t('auth.register.firstName') }}</label>
        <input v-model="form.first_name" type="text" class="form-input" :class="{ '!border-passenger-400': fieldError('first_name') }" />
        <p v-if="fieldError('first_name')" class="form-error">{{ fieldError('first_name') }}</p>
      </div>
      <div>
        <label class="form-label">{{ t('auth.register.lastName') }}</label>
        <input v-model="form.last_name" type="text" class="form-input" :class="{ '!border-passenger-400': fieldError('last_name') }" />
        <p v-if="fieldError('last_name')" class="form-error">{{ fieldError('last_name') }}</p>
      </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label class="form-label">{{ t('auth.register.cin') }}</label>
        <input v-model="form.cin" type="text" inputmode="numeric" maxlength="8" class="form-input" :class="{ '!border-passenger-400': fieldError('cin') }" />
        <p v-if="fieldError('cin')" class="form-error">{{ fieldError('cin') }}</p>
      </div>
      <div>
        <label class="form-label">{{ t('auth.register.age') }}</label>
        <input v-model.number="form.age" type="number" min="16" max="120" class="form-input" :class="{ '!border-passenger-400': fieldError('age') }" />
        <p v-if="fieldError('age')" class="form-error">{{ fieldError('age') }}</p>
      </div>
    </div>

    <div>
      <label class="form-label">{{ t('auth.register.username') }}</label>
      <input
        v-model="form.username"
        type="text"
        class="form-input"
        :class="{ '!border-passenger-400': fieldError('username') || usernameCheck.available === false }"
        @input="usernameCheck.reset()"
        @blur="onUsernameBlur"
      />
      <p v-if="fieldError('username')" class="form-error">{{ fieldError('username') }}</p>
      <p v-else-if="usernameCheck.available === false" class="form-error">
        {{ t('auth.register.usernameTaken') }}
        <button type="button" class="font-semibold underline" @click="useSuggestedUsername">
          {{ t('auth.register.usernameUseSuggestion', { suggestion: usernameCheck.suggestion }) }}
        </button>
      </p>
    </div>

    <div>
      <label class="form-label">{{ t('auth.register.gender') }}</label>
      <div class="grid grid-cols-2 gap-3">
        <label
          class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium"
          :class="form.gender === 'female' ? 'border-brand-600 bg-brand-50 text-brand-800' : 'border-slate-300 text-slate-600 hover:bg-slate-50'"
        >
          <input v-model="form.gender" type="radio" value="female" class="hidden" />
          {{ t('auth.register.genderFemale') }}
        </label>
        <label
          class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium"
          :class="form.gender === 'male' ? 'border-brand-600 bg-brand-50 text-brand-800' : 'border-slate-300 text-slate-600 hover:bg-slate-50'"
        >
          <input v-model="form.gender" type="radio" value="male" class="hidden" />
          {{ t('auth.register.genderMale') }}
        </label>
      </div>
    </div>

    <div class="flex justify-end border-t border-slate-100 pt-4">
      <button type="submit" class="btn-primary" :disabled="saving">
        <svg v-if="saving" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        {{ t('profile.saveChanges') }}
      </button>
    </div>
  </form>
</template>
