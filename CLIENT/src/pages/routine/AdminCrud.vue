<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Routine (Admin)</h1>
      <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">+ Add Slot</button>
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
        <button class="px-3 py-2 rounded bg-white border text-sm w-full" @click="load">Refresh</button>
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
            <th class="text-left p-3">Action</th>
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
            <td class="p-3">
              <button class="text-blue-700" @click="openEdit(r)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(r)">Delete</button>
            </td>
          </tr>
          <tr v-if="routines.length===0">
            <td class="p-4 text-slate-500" colspan="8">No routines</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-lg p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Slot" : "Add Slot" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div class="grid gap-3 md:grid-cols-2">
          <div>
            <label class="text-xs text-slate-600">Class</label>
            <select v-model.number="form.class_id" class="w-full border rounded px-3 py-2 text-sm">
              <option disabled :value="0">Select class</option>
              <option v-for="c in masters.classes" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-600">Section</label>
            <select v-model.number="form.section_id" class="w-full border rounded px-3 py-2 text-sm">
              <option :value="null">None</option>
              <option v-for="s in sectionOptionsForForm" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-600">Weekday</label>
            <select v-model.number="form.weekday" class="w-full border rounded px-3 py-2 text-sm">
              <option v-for="d in days" :key="d.v" :value="d.v">{{ d.t }}</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-600">Room</label>
            <input v-model="form.room" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Start</label>
            <input type="time" v-model="form.start_time" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">End</label>
            <input type="time" v-model="form.end_time" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-slate-600">Subject</label>
            <select v-model.number="form.subject_id" class="w-full border rounded px-3 py-2 text-sm">
              <option disabled :value="0">Select subject</option>
              <option v-for="s in masters.subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-slate-600">Teacher ID (optional)</label>
            <input v-model.number="form.teacher_id" class="w-full border rounded px-3 py-2 text-sm" placeholder="Teacher id" />
            <p class="text-xs text-slate-500 mt-1">Tip: open Teachers page and use teacher id from list.</p>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button class="px-3 py-2 rounded border bg-white text-sm" @click="close">Cancel</button>
          <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="save">Save</button>
        </div>

        <p v-if="msg" class="text-sm" :class="ok ? 'text-green-700' : 'text-red-700'">{{ msg }}</p>
      </div>
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

const showModal = ref(false);
const editingId = ref(null);
const msg = ref("");
const ok = ref(true);

const days = [
  { v: 1, t: "Mon" }, { v: 2, t: "Tue" }, { v: 3, t: "Wed" }, { v: 4, t: "Thu" },
  { v: 5, t: "Fri" }, { v: 6, t: "Sat" }, { v: 7, t: "Sun" }
];

const form = ref({
  class_id: 0,
  section_id: null,
  weekday: 1,
  start_time: "09:00",
  end_time: "10:00",
  subject_id: 0,
  teacher_id: null,
  room: "",
});

const sectionOptions = computed(() => {
  if (!classId.value) return masters.value.sections;
  return masters.value.sections.filter(s => s.class_id === classId.value);
});
const sectionOptionsForForm = computed(() => {
  if (!form.value.class_id) return [];
  return masters.value.sections.filter(s => s.class_id === form.value.class_id);
});

function dayName(d){ return ["","Mon","Tue","Wed","Thu","Fri","Sat","Sun"][d] || d; }

function resetForm() {
  form.value = {
    class_id: masters.value.classes[0]?.id || 0,
    section_id: null,
    weekday: 1,
    start_time: "09:00",
    end_time: "10:00",
    subject_id: masters.value.subjects[0]?.id || 0,
    teacher_id: null,
    room: "",
  };
}

function openCreate(){
  msg.value=""; ok.value=true;
  editingId.value=null;
  resetForm();
  showModal.value=true;
}
function openEdit(r){
  msg.value=""; ok.value=true;
  editingId.value=r.id;
  form.value = {
    class_id: r.class_id,
    section_id: r.section_id,
    weekday: r.weekday,
    start_time: r.start_time,
    end_time: r.end_time,
    subject_id: r.subject_id,
    teacher_id: r.teacher_id,
    room: r.room || "",
  };
  showModal.value=true;
}
function close(){ showModal.value=false; }

async function load(){
  const params = {};
  if (classId.value) params.class_id = classId.value;
  if (sectionId.value) params.section_id = sectionId.value;
  const { data } = await api.get("/routines", { params });
  routines.value = data;
}

async function save(){
  msg.value="";
  try{
    const payload = { ...form.value };
    if (!payload.section_id) payload.section_id = null;
    if (!payload.teacher_id) payload.teacher_id = null;

    if (editingId.value) await api.put(`/routines/${editingId.value}`, payload);
    else await api.post("/routines", payload);

    ok.value=true; msg.value="Saved!";
    await load();
    setTimeout(()=>showModal.value=false, 400);
  } catch(e){
    ok.value=false;
    msg.value = e?.response?.data?.message || "Save failed";
  }
}
async function remove(r){
  if (!confirm("Delete this slot?")) return;
  await api.delete(`/routines/${r.id}`);
  await load();
}

onMounted(async () => {
  masters.value = await fetchMasters().catch(() => ({ classes: [], sections: [], subjects: [] }));
  await load();
});
</script>
