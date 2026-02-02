<template>
  <button
    class="px-3 py-2 rounded text-sm border bg-slate-900 text-white hover:opacity-90 disabled:opacity-50"
    :disabled="disabled"
    :type="type"
  >
    <slot />
  </button>
</template>

<script setup>
defineProps({ disabled: Boolean, type: { type: String, default: "button" } });
</script>
