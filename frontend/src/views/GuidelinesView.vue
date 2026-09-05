<script setup>
import { useI18n } from 'vue-i18n'

import GuidelineSection from '@/components/guidelines/GuidelineSection.vue'

const { t, tm } = useI18n()
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-6">
    <div class="-mt-3">
      <p class="text-lg font-semibold text-slate-800">{{ t('guidelines.pageTitle') }}</p>
      <p class="mt-1 text-sm text-slate-500">{{ t('guidelines.pageSubtitle') }}</p>
    </div>

    <GuidelineSection :title="t('guidelines.gettingStarted.title')">
      <p>{{ t('guidelines.gettingStarted.body') }}</p>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.browsing.title')">
      <p>{{ t('guidelines.browsing.intro') }}</p>
      <p class="rounded-lg bg-slate-50 px-3 py-2 text-slate-600">{{ t('guidelines.browsing.statusNote') }}</p>
      <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-lg border border-passenger-200 bg-passenger-50 p-3">
          <p class="text-sm font-semibold text-passenger-700">{{ t('guidelines.browsing.passengerTitle') }}</p>
          <p class="mt-1 text-sm text-slate-600">{{ t('guidelines.browsing.passengerBody') }}</p>
        </div>
        <div class="rounded-lg border border-driver-200 bg-driver-50 p-3">
          <p class="text-sm font-semibold text-driver-700">{{ t('guidelines.browsing.driverTitle') }}</p>
          <p class="mt-1 text-sm text-slate-600">{{ t('guidelines.browsing.driverBody') }}</p>
        </div>
      </div>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.creating.title')">
      <p>{{ t('guidelines.creating.body') }}</p>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.reservation.title')">
      <p>{{ t('guidelines.reservation.default') }}</p>

      <div>
        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">
          {{ t('guidelines.reservation.howItWorksTitle') }}
        </p>
        <ol class="space-y-2">
          <li v-for="(step, index) in tm('guidelines.reservation.steps')" :key="index" class="flex gap-2.5">
            <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
              {{ index + 1 }}
            </span>
            <span>{{ step }}</span>
          </li>
        </ol>
      </div>

      <div class="rounded-lg border border-brand-200 bg-brand-50 p-3">
        <p class="text-sm font-semibold text-brand-800">{{ t('guidelines.reservation.clarificationTitle') }}</p>
        <p class="mt-1 text-brand-800/90">{{ t('guidelines.reservation.clarificationBody') }}</p>
      </div>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.history.title')">
      <p>{{ t('guidelines.history.body') }}</p>
      <div class="rounded-lg bg-slate-100 p-3">
        <p class="text-sm font-semibold text-slate-700">{{ t('guidelines.history.grayTitle') }}</p>
        <p class="mt-1 text-slate-600">{{ t('guidelines.history.grayBody') }}</p>
      </div>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.profile.title')">
      <p>{{ t('guidelines.profile.body') }}</p>
    </GuidelineSection>

    <GuidelineSection :title="t('guidelines.support.title')">
      <p>{{ t('guidelines.support.body') }}</p>
    </GuidelineSection>
  </div>
</template>
