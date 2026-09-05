<script setup>
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'

import { fetchDashboardStats } from '@/api/admin'
import DonutChart from '@/components/admin/DonutChart.vue'
import PostsPerDayChart from '@/components/admin/PostsPerDayChart.vue'
import StatCard from '@/components/admin/StatCard.vue'
import { extractErrorMessage } from '@/lib/http'

const { t } = useI18n()

const stats = ref(null)
const loading = ref(true)
const error = ref('')

async function load() {
  loading.value = true
  error.value = ''
  try {
    const { data } = await fetchDashboardStats()
    stats.value = data
  } catch (err) {
    error.value = extractErrorMessage(err, t('admin.dashboard.error'))
  } finally {
    loading.value = false
  }
}

onMounted(load)

// Passenger/Driver keep the same red/blue used for their status everywhere
// else in the app (publication cards, badges) - identity stays fixed to
// the entity rather than being reassigned per chart.
const statusSegments = computed(() => {
  if (!stats.value) return []
  return [
    {
      label: t('admin.dashboard.passengerPosts'),
      value: stats.value.posts.passenger,
      percentage: stats.value.posts.passenger_percentage,
      color: '#e33e3e',
    },
    {
      label: t('admin.dashboard.driverPosts'),
      value: stats.value.posts.driver,
      percentage: stats.value.posts.driver_percentage,
      color: '#2f74e0',
    },
  ]
})

const reservationSegments = computed(() => {
  if (!stats.value) return []
  return [
    {
      label: t('admin.dashboard.available'),
      value: stats.value.driver_reservations.available,
      percentage: stats.value.driver_reservations.available_percentage,
      color: '#10b981',
    },
    {
      label: t('admin.dashboard.reserved'),
      value: stats.value.driver_reservations.reserved,
      percentage: stats.value.driver_reservations.reserved_percentage,
      color: '#94a3b8',
    },
  ]
})
</script>

<template>
  <div class="space-y-6">
    <p class="-mt-3 text-sm text-slate-500">{{ t('admin.dashboard.subtitle') }}</p>

    <div v-if="loading" class="flex h-40 items-center justify-center text-sm text-slate-400">
      {{ t('admin.dashboard.loading') }}
    </div>

    <div v-else-if="error" class="rounded-xl border border-passenger-200 bg-passenger-50 p-6 text-center text-sm text-passenger-700">
      {{ error }}
      <button type="button" class="mt-3 block w-full font-semibold underline" @click="load">
        {{ t('admin.dashboard.retry') }}
      </button>
    </div>

    <template v-else-if="stats">
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <StatCard :label="t('admin.dashboard.totalUsers')" :value="stats.users.total" />
        <StatCard :label="t('admin.dashboard.totalPosts')" :value="stats.posts.total" />
        <StatCard :label="t('admin.dashboard.passengerPosts')" :value="stats.posts.passenger" />
        <StatCard :label="t('admin.dashboard.driverPosts')" :value="stats.posts.driver" />
        <StatCard
          :label="t('admin.dashboard.available')"
          :value="stats.driver_reservations.available"
          :caption="`${stats.driver_reservations.available_percentage}% ${t('admin.dashboard.ofDriverPosts')}`"
        />
        <StatCard
          :label="t('admin.dashboard.reserved')"
          :value="stats.driver_reservations.reserved"
          :caption="`${stats.driver_reservations.reserved_percentage}% ${t('admin.dashboard.ofDriverPosts')}`"
        />
      </div>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-slate-500">
          {{ t('admin.dashboard.passengerVsDriver') }}
        </h2>
        <DonutChart :segments="statusSegments" :center-label="t('admin.dashboard.totalPosts')" :empty-message="t('admin.dashboard.noPosts')" />
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <h2 class="mb-5 text-sm font-semibold uppercase tracking-wide text-slate-500">
          {{ t('admin.dashboard.driverAvailability') }}
        </h2>
        <DonutChart :segments="reservationSegments" :center-label="t('admin.dashboard.driverPosts')" :empty-message="t('admin.dashboard.noPosts')" />
      </section>

      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-card">
        <div class="mb-5 flex items-baseline justify-between">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">
            {{ t('admin.dashboard.postsPerDay') }}
          </h2>
          <span class="text-xs text-slate-400">{{ t('admin.dashboard.postsPerDayCaption') }}</span>
        </div>
        <PostsPerDayChart :data="stats.posts_per_day" :empty-message="t('admin.dashboard.noPosts')" />
      </section>
    </template>
  </div>
</template>
