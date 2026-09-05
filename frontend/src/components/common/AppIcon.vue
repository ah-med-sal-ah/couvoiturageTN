<script setup>
import { computed } from 'vue'

/**
 * Renders one of the supplied /images/*.png glyphs.
 *
 * The provided icons are solid black shapes on a transparent background, so
 * by default we render them as a CSS mask - this lets a single asset adopt
 * any current-color state (active/inactive nav, etc.) without needing
 * separate colored variants. Pass `raw` for assets that already carry their
 * own colors (the app logo, the two-tone "+" button).
 *
 * A couple of names (e.g. "dashboard") have no supplied image asset and
 * render as an inline SVG instead, matching the same solid single-color
 * style so they sit comfortably next to the PNG-based icons.
 */
const props = defineProps({
  name: { type: String, required: true },
  raw: { type: Boolean, default: false },
  size: { type: [String, Number], default: 24 },
})

// No supplied PNG exists for these - inline SVG paths in the same solid style.
const INLINE_ICONS = {
  dashboard: '<rect x="3" y="12" width="4.5" height="9" rx="1"/><rect x="9.75" y="7" width="4.5" height="14" rx="1"/><rect x="16.5" y="3" width="4.5" height="18" rx="1"/>',
}

const src = computed(() => `/images/${props.name}.png`)
const dimension = computed(() => (typeof props.size === 'number' ? `${props.size}px` : props.size))
const inlineMarkup = computed(() => INLINE_ICONS[props.name])
</script>

<template>
  <img v-if="raw" :src="src" :alt="name" :style="{ width: dimension, height: dimension }" class="object-contain" />
  <svg
    v-else-if="inlineMarkup"
    role="img"
    :aria-label="name"
    viewBox="0 0 24 24"
    fill="currentColor"
    class="inline-block shrink-0"
    :style="{ width: dimension, height: dimension }"
    v-html="inlineMarkup"
  />
  <span
    v-else
    role="img"
    :aria-label="name"
    class="inline-block shrink-0 bg-current"
    :style="{
      width: dimension,
      height: dimension,
      WebkitMaskImage: `url(${src})`,
      maskImage: `url(${src})`,
      WebkitMaskSize: 'contain',
      maskSize: 'contain',
      WebkitMaskRepeat: 'no-repeat',
      maskRepeat: 'no-repeat',
      WebkitMaskPosition: 'center',
      maskPosition: 'center',
    }"
  />
</template>
