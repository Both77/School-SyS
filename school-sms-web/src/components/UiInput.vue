<template>
  <input
    class="w-full border rounded px-3 py-2 text-sm"
    :type="type"
    :placeholder="placeholder"
    v-model="model"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: [String, Number],
  type: { type: String, default: "text" },
  placeholder: { type: String, default: "" },
});
const emit = defineEmits(["update:modelValue"]);
const model = computed({
  get: () => props.modelValue,
  set: (v) => emit("update:modelValue", v),
});
import { computed } from "vue";
</script>
