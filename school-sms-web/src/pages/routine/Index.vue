<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Class Routine</h1>
      <button class="px-3 py-2 rounded bg-white border text-sm" @click="load">Refresh</button>
    </div>

    <div class="bg-white rounded shadow p-4 grid gap-3 md:grid-cols-3">
      <div>
        <label class="text-xs text-slate-600">Class</label>
        <select v-model.number="classId" class="w-full border rounded px-3 py-2 text-sm">
          <option :value="0">All</option>
          <option v-for="c in masters.classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-600">Section</label>
        <select v-model.number="sectionId" class="w-full border rounded px-3 py-2 text-sm">
          <option :value="0">All</option>
          <option v-for="s in sectionOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <div class="flex items-end">
        <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm w-full" @click="load">Filter</button>
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Day</th>
            <th class="text-left p-3">Time</th>
            <th class="text-left p-3">Class</th>
            <th class="text-left p-3">Section</th>
            <th class="text-left p-3">Subject</th>
            <th class="text-left p-3">Teacher</th>
            <th class="text-left p-3">Room</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in routines" :key="r.id" class="border-t">
            <td class="p-3">{{ dayName(r.weekday) }}</td>
            <td class="p-3">{{ r.start_time }} - {{ r.end_time }}</td>
            <td class="p-3">{{ r.class?.name }}</td>
            <td class="p-3">{{ r.section?.name || "-" }}</td>
            <td class="p-3">{{ r.subject?.name }}</td>
            <td class="p-3">{{ r.teacher?.user?.name || "-" }}</td>
            <td class="p-3">{{ r.room || "-" }}</td>
          </tr>
          <tr v-if="routines.length===0">
            <td class="p-4 text-slate-500" colspan="7">No routines</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { api } from "@lib/api";
import { fetchMasters } from "@lib/masters";

const masters = ref({ classes: [], sections: [], subjects: [] });
const routines = ref([]);

const classId = ref(0);
const sectionId = ref(0);

const sectionOptions = computed(() => {
  if (!classId.value) return masters.value.sections;
  return masters.value.sections.filter(s => s.class_id === classId.value);
});

function dayName(d) {
  return ["", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"][d] || d;
}

async function load() {
  const params = {};
  if (classId.value) params.class_id = classId.value;
  if (sectionId.value) params.section_id = sectionId.value;
  const { data } = await api.get("/routines", { params });
  routines.value = data;
}

onMounted(async () => {
  masters.value = await fetchMasters().catch(() => ({ classes: [], sections: [], subjects: [] }));
  await load();
});
</script>
