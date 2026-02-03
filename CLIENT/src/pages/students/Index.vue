<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Students</h1>
      <div class="flex items-center gap-2">
        <button class="px-3 py-2 rounded bg-white border text-sm" @click="load">Refresh</button>
        <button v-if="isAdmin" class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">
          + Add Student
        </button>
      </div>
    </div>

    <div class="bg-white rounded shadow p-4 grid gap-3 md:grid-cols-3">
      <div>
        <label class="text-xs text-slate-600">Class</label>
        <select v-model.number="filterClass" class="w-full border rounded px-3 py-2 text-sm">
          <option :value="0">All</option>
          <option v-for="c in masters.classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-600">Section</label>
        <select v-model.number="filterSection" class="w-full border rounded px-3 py-2 text-sm">
          <option :value="0">All</option>
          <option v-for="s in sectionOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-600">Search (name/admission)</label>
        <input v-model="q" class="w-full border rounded px-3 py-2 text-sm" placeholder="Search..." />
      </div>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Name</th>
            <th class="text-left p-3">Admission No</th>
            <th class="text-left p-3">Class</th>
            <th class="text-left p-3">Section</th>
            <th class="text-left p-3" v-if="isAdmin">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in filtered" :key="s.id" class="border-t">
            <td class="p-3">{{ s.name }}</td>
            <td class="p-3">{{ s.admission_no }}</td>
            <td class="p-3">{{ s.class?.name }}</td>
            <td class="p-3">{{ s.section?.name || "-" }}</td>
            <td class="p-3" v-if="isAdmin">
              <button class="text-blue-700" @click="openEdit(s)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(s)">Delete</button>
            </td>
          </tr>
          <tr v-if="filtered.length===0">
            <td class="p-4 text-slate-500" :colspan="isAdmin ? 5 : 4">No students</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-lg p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Student" : "Add Student" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div class="grid gap-3 md:grid-cols-2">
          <div>
            <label class="text-xs text-slate-600">Name</label>
            <input v-model="form.name" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Admission No</label>
            <input v-model="form.admission_no" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
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
            <label class="text-xs text-slate-600">DOB</label>
            <input type="date" v-model="form.dob" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Gender</label>
            <select v-model="form.gender" class="w-full border rounded px-3 py-2 text-sm">
              <option value="">-</option>
              <option>male</option>
              <option>female</option>
              <option>other</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-slate-600">Guardian phone</label>
            <input v-model="form.guardian_phone" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-slate-600">Address</label>
            <textarea v-model="form.address" class="w-full border rounded px-3 py-2 text-sm" rows="2"></textarea>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
          <button class="px-3 py-2 rounded border bg-white text-sm" @click="close">Cancel</button>
          <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="save">
            Save
          </button>
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
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const isAdmin = computed(() => auth.user?.role === "admin");

const masters = ref({ classes: [], sections: [], subjects: [] });
const students = ref([]);

const filterClass = ref(0);
const filterSection = ref(0);
const q = ref("");

const showModal = ref(false);
const editingId = ref(null);
const form = ref({
  admission_no: "",
  name: "",
  class_id: 0,
  section_id: null,
  dob: "",
  gender: "",
  guardian_phone: "",
  address: "",
});
const msg = ref("");
const ok = ref(true);

const sectionOptions = computed(() => {
  if (!filterClass.value) return masters.value.sections;
  return masters.value.sections.filter(s => s.class_id === filterClass.value);
});

const sectionOptionsForForm = computed(() => {
  if (!form.value.class_id) return [];
  return masters.value.sections.filter(s => s.class_id === form.value.class_id);
});

const filtered = computed(() => {
  const term = q.value.trim().toLowerCase();
  return students.value.filter(s => {
    if (filterClass.value && s.class_id !== filterClass.value) return false;
    if (filterSection.value && s.section_id !== filterSection.value) return false;
    if (!term) return true;
    return (s.name || "").toLowerCase().includes(term) || (s.admission_no || "").toLowerCase().includes(term);
  });
});

function resetForm() {
  form.value = {
    admission_no: "",
    name: "",
    class_id: masters.value.classes[0]?.id || 0,
    section_id: null,
    dob: "",
    gender: "",
    guardian_phone: "",
    address: "",
  };
}

function openCreate() {
  msg.value = "";
  ok.value = true;
  editingId.value = null;
  resetForm();
  showModal.value = true;
}

function openEdit(s) {
  msg.value = "";
  ok.value = true;
  editingId.value = s.id;
  form.value = {
    admission_no: s.admission_no,
    name: s.name,
    class_id: s.class_id,
    section_id: s.section_id,
    dob: s.dob || "",
    gender: s.gender || "",
    guardian_phone: s.guardian_phone || "",
    address: s.address || "",
  };
  showModal.value = true;
}

function close() { showModal.value = false; }

async function load() {
  const { data } = await api.get("/students");
  students.value = data.data ?? data;
}

async function save() {
  msg.value = "";
  try {
    const payload = { ...form.value };
    if (!payload.section_id) payload.section_id = null;

    if (editingId.value) {
      await api.put(`/students/${editingId.value}`, payload);
    } else {
      await api.post("/students", payload);
    }
    ok.value = true;
    msg.value = "Saved!";
    await load();
    setTimeout(() => (showModal.value = false), 400);
  } catch (e) {
    ok.value = false;
    msg.value = e?.response?.data?.message || "Save failed";
  }
}

async function remove(s) {
  if (!confirm(`Delete ${s.name}?`)) return;
  try {
    await api.delete(`/students/${s.id}`);
    await load();
  } catch {
    alert("Delete failed");
  }
}

onMounted(async () => {
  masters.value = await fetchMasters().catch(() => ({ classes: [], sections: [], subjects: [] }));
  await load();
});
</script>
