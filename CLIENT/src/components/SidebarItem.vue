<template>
  <RouterLink
    :to="to"
    class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800 transition"
    active-class="bg-slate-800"
  >
    <span class="w-6 text-center">{{ icon }}</span>
    <span class="text-sm">{{ label }}</span>
    <span class="ml-auto text-slate-500">›</span>
  </RouterLink>
</template>

<script setup>
defineProps({ to: String, icon: String, label: String });
</script>
