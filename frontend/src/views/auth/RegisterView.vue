<script setup>
import { computed, reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

import PasswordStrengthMeter from '@/components/common/PasswordStrengthMeter.vue'
import AppIcon from '@/components/common/AppIcon.vue'
import { usePhotoUpload } from '@/composables/usePhotoUpload'
import { useUsernameCheck } from '@/composables/useUsernameCheck'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  profile_photo: null,
  first_name: '',
  last_name: '',
  cin: '',
  age: '',
  username: '',
  password: '',
  password_confirmation: '',
  gender: '',
})

const photo = usePhotoUpload()
const usernameCheck = useUsernameCheck()
const errors = ref({})
const errorMessage = ref('')
const fileInput = ref(null)

function fieldError(field) {
  if (field === 'profile_photo') {
    return errors.value?.profile_photo?.[0] || photo.error || ''
  }
  return errors.value?.[field]?.[0] || ''
}

function onUsernameBlur() {
  usernameCheck.check(form.username)
}

function useSuggestedUsername() {
  form.username = usernameCheck.suggestion
  usernameCheck.reset()
}

function onPhotoChange(event) {
  photo.select(event)
  form.profile_photo = photo.file
}

function removePhoto() {
  photo.clear(fileInput.value)
  form.profile_photo = null
}

const passwordsMatch = computed(
  () => !form.password_confirmation || form.password === form.password_confirmation,
)

async function submit() {
  errorMessage.value = ''
  errors.value = {}

  if (form.password !== form.password_confirmation) {
    errors.value = { password_confirmation: [t('auth.register.passwordMismatch')] }
    return
  }

  try {
    await auth.register(form)
    router.push({ name: 'home' })
  } catch (error) {
    errorMessage.value = error.message
    errors.value = error.fields || {}
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-10">
    <div class="w-full max-w-lg">
      <div class="mb-8 flex flex-col items-center text-center">
        <img src="/images/couvoiturage.png" alt="CovoiturageTN" class="mb-3 h-14 w-14 rounded-xl object-cover shadow-card" />
        <h1 class="text-xl font-semibold text-slate-900">{{ t('auth.register.title') }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ t('auth.register.subtitle') }}</p>
      </div>

      <form class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-card" @submit.prevent="submit" novalidate>
        <p v-if="errorMessage" class="rounded-lg bg-passenger-50 px-3 py-2 text-sm font-medium text-passenger-700">
          {{ errorMessage }}
        </p>

        <div>
          <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-slate-400">
              <img v-if="photo.preview" :src="photo.preview" alt="" class="h-full w-full object-cover" />
              <AppIcon v-else name="profile" :size="28" />
            </div>
            <div>
              <label class="form-label mb-1">{{ t('auth.register.profilePhoto') }}</label>
              <div class="flex gap-2">
                <label class="btn-secondary cursor-pointer text-xs">
                  {{ t('auth.register.uploadPhoto') }}
                  <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
                </label>
                <button v-if="photo.preview" type="button" class="btn-danger-ghost text-xs" @click="removePhoto">
                  {{ t('auth.register.removePhoto') }}
                </button>
              </div>
            </div>
          </div>
          <p v-if="fieldError('profile_photo')" class="form-error">{{ fieldError('profile_photo') }}</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="register-first-name" class="form-label">{{ t('auth.register.firstName') }} *</label>
            <input id="register-first-name" v-model="form.first_name" name="first_name" type="text" required class="form-input" :class="{ '!border-passenger-400': fieldError('first_name') }" />
            <p v-if="fieldError('first_name')" class="form-error">{{ fieldError('first_name') }}</p>
          </div>
          <div>
            <label for="register-last-name" class="form-label">{{ t('auth.register.lastName') }} *</label>
            <input id="register-last-name" v-model="form.last_name" name="last_name" type="text" required class="form-input" :class="{ '!border-passenger-400': fieldError('last_name') }" />
            <p v-if="fieldError('last_name')" class="form-error">{{ fieldError('last_name') }}</p>
          </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="register-cin" class="form-label">{{ t('auth.register.cin') }} *</label>
            <input
              id="register-cin"
              v-model="form.cin"
              name="cin"
              type="text"
              inputmode="numeric"
              maxlength="8"
              required
              class="form-input"
              :class="{ '!border-passenger-400': fieldError('cin') }"
            />
            <p v-if="fieldError('cin')" class="form-error">{{ fieldError('cin') }}</p>
          </div>
          <div>
            <label for="register-age" class="form-label">{{ t('auth.register.age') }} *</label>
            <input
              id="register-age"
              v-model.number="form.age"
              name="age"
              type="number"
              min="16"
              max="120"
              required
              class="form-input"
              :class="{ '!border-passenger-400': fieldError('age') }"
            />
            <p v-if="fieldError('age')" class="form-error">{{ fieldError('age') }}</p>
          </div>
        </div>

        <div>
          <label for="register-username" class="form-label">
            {{ t('auth.register.username') }} *
            <span class="font-normal text-passenger-600">{{ t('auth.register.usernameHint') }}</span>
          </label>
          <input
            id="register-username"
            v-model="form.username"
            name="username"
            type="text"
            required
            autocomplete="username"
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
          <label class="form-label">{{ t('auth.register.gender') }} *</label>
          <div class="grid grid-cols-2 gap-3">
            <label
              class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium"
              :class="form.gender === 'female' ? 'border-brand-600 bg-brand-50 text-brand-800' : 'border-slate-300 text-slate-600 hover:bg-slate-50'"
            >
              <input v-model="form.gender" name="gender" type="radio" value="female" class="hidden" />
              {{ t('auth.register.genderFemale') }}
            </label>
            <label
              class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-medium"
              :class="form.gender === 'male' ? 'border-brand-600 bg-brand-50 text-brand-800' : 'border-slate-300 text-slate-600 hover:bg-slate-50'"
            >
              <input v-model="form.gender" name="gender" type="radio" value="male" class="hidden" />
              {{ t('auth.register.genderMale') }}
            </label>
          </div>
          <p v-if="fieldError('gender')" class="form-error">{{ fieldError('gender') }}</p>
        </div>

        <div>
          <label for="register-password" class="form-label">{{ t('auth.register.password') }} *</label>
          <input
            id="register-password"
            v-model="form.password"
            name="password"
            type="password"
            required
            autocomplete="new-password"
            class="form-input"
            :class="{ '!border-passenger-400': fieldError('password') }"
          />
          <PasswordStrengthMeter :password="form.password" />
          <p class="mt-1 text-xs text-slate-400">{{ t('password.requirements') }}</p>
          <p v-if="fieldError('password')" class="form-error">{{ fieldError('password') }}</p>
        </div>

        <div>
          <label for="register-password-confirmation" class="form-label">{{ t('auth.register.repeatPassword') }} *</label>
          <input
            id="register-password-confirmation"
            v-model="form.password_confirmation"
            name="password_confirmation"
            type="password"
            required
            autocomplete="new-password"
            class="form-input"
            :class="{ '!border-passenger-400': !passwordsMatch || fieldError('password_confirmation') }"
          />
          <p v-if="!passwordsMatch" class="form-error">{{ t('auth.register.passwordMismatch') }}</p>
          <p v-else-if="fieldError('password_confirmation')" class="form-error">{{ fieldError('password_confirmation') }}</p>
        </div>

        <button type="submit" class="btn-primary w-full" :disabled="auth.registerLoading">
          <svg v-if="auth.registerLoading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
          </svg>
          {{ auth.registerLoading ? t('auth.register.submitting') : t('auth.register.submit') }}
        </button>
      </form>

      <p class="mt-5 text-center text-sm text-slate-500">
        {{ t('auth.register.haveAccount') }}
        <RouterLink :to="{ name: 'login' }" class="font-semibold text-brand-700 hover:text-brand-800">
          {{ t('auth.register.signIn') }}
        </RouterLink>
      </p>
    </div>
  </div>
</template>
