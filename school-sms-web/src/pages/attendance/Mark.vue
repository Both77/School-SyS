<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Mark Attendance</h1>
      <button class="px-3 py-2 rounded bg-white border text-sm" @click="reloadMasters">Reload Masters</button>
    </div>

    <div class="bg-white rounded shadow p-4 grid gap-3 md:grid-cols-4">
      <div>
        <label class="text-xs text-slate-600">Date</label>
        <input type="date" v-model="date" class="w-full border rounded px-3 py-2 text-sm" />
      </div>
      <div>
        <label class="text-xs text-slate-600">Class</label>
        <select v-model.number="classId" class="w-full border rounded px-3 py-2 text-sm">
          <option disabled :value="0">Select class</option>
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
      <div class="flex items-end gap-2">
        <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm w-full" @click="loadStudents">
          Load
        </button>
      </div>
    </div>

    <div v-if="items.length" class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Student</th>
            <th class="text-left p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="it in items" :key="it.student_id" class="border-t">
            <td class="p-3">{{ it.name }}</td>
            <td class="p-3">
              <select v-model="it.status" class="border rounded px-2 py-1 text-sm">
                <option value="present">present</option>
                <option value="absent">absent</option>
                <option value="late">late</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="p-3 border-t flex items-center justify-end gap-3">
        <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="save">Save</button>
      </div>
    </div>

    <p v-if="msg" class="text-sm" :class="ok ? 'text-green-700' : 'text-red-700'">{{ msg }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { api } from "@lib/api";
import { fetchMasters } from "@lib/masters";

const date = ref(new Date().toISOString().slice(0,10));
const classId = ref(0);
const sectionId = ref(0);

const masters = ref({ classes: [], sections: [], subjects: [] });
const students = ref([]);
const items = ref([]);

const msg = ref("");
const ok = ref(true);

const sectionOptions = computed(() => {
  if (!classId.value) return masters.value.sections;
  return masters.value.sections.filter(s => s.class_id === classId.value);
});

async function reloadMasters() {
  masters.value = await fetchMasters().catch(() => ({ classes: [], sections: [], subjects: [] }));
  if (!classId.value) classId.value = masters.value.classes[0]?.id || 0;
}

async function loadStudents() {
  msg.value = "";
  if (!classId.value) { ok.value=false; msg.value="Select a class"; return; }

  const res = await api.get("/students");
  const list = res.data.data ?? res.data;
  students.value = list;

  const filtered = list.filter(s =>
    s.class_id === classId.value && (!sectionId.value || s.section_id === sectionId.value)
  );

  // Try load existing attendance for that date/class/section
  let existing = [];
  try {
    const { data } = await api.get("/attendance/list", {
      params: { date: date.value, class_id: classId.value, section_id: sectionId.value || undefined }
    });
    existing = data;
  } catch {}

  const mapExisting = new Map(existing.map(a => [a.student_id, a.status]));
  items.value = filtered.map(s => ({
    student_id: s.id,
    name: s.name,
    status: mapExisting.get(s.id) || "present"
  }));
}

async function save() {
  msg.value = "";
  try {
    await api.post("/attendance/mark", {
      date: date.value,
      class_id: classId.value,
      section_id: sectionId.value || null,
      items: items.value.map(i => ({ student_id: i.student_id, status: i.status })),
    });
    ok.value = true;
    msg.value = "Attendance saved!";
  } catch (e) {
    ok.value = false;
    msg.value = e?.response?.data?.message || "Failed to save attendance";
  }
}

onMounted(async () => {
  await reloadMasters();
});
</script>
