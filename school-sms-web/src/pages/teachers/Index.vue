<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Teachers</h1>
      <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">+ Add Teacher</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Name</th>
            <th class="text-left p-3">Email</th>
            <th class="text-left p-3">Employee No</th>
            <th class="text-left p-3">Phone</th>
            <th class="text-left p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in teachers" :key="t.id" class="border-t">
            <td class="p-3">{{ t.user?.name }}</td>
            <td class="p-3">{{ t.user?.email }}</td>
            <td class="p-3">{{ t.employee_no }}</td>
            <td class="p-3">{{ t.phone || "-" }}</td>
            <td class="p-3">
              <button class="text-blue-700" @click="openEdit(t)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(t)">Delete</button>
            </td>
          </tr>
          <tr v-if="teachers.length===0">
            <td class="p-4 text-slate-500" colspan="5">No teachers</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-lg p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Teacher" : "Add Teacher" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div class="grid gap-3 md:grid-cols-2">
          <div>
            <label class="text-xs text-slate-600">Name</label>
            <input v-model="form.name" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Email</label>
            <input v-model="form.email" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Employee No</label>
            <input v-model="form.employee_no" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Phone</label>
            <input v-model="form.phone" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-slate-600">Designation</label>
            <input v-model="form.designation" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="text-xs text-slate-600">
              Password {{ editingId ? "(leave blank to keep)" : "" }}
            </label>
            <input v-model="form.password" type="password" class="w-full border rounded px-3 py-2 text-sm" />
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
import { ref, onMounted } from "vue";
import { api } from "@lib/api";

const teachers = ref([]);
const showModal = ref(false);
const editingId = ref(null);
const form = ref({ name:"", email:"", password:"", employee_no:"", phone:"", designation:"" });
const msg = ref("");
const ok = ref(true);

function openCreate() {
  msg.value=""; ok.value=true;
  editingId.value=null;
  form.value={ name:"", email:"", password:"", employee_no:"", phone:"", designation:"" };
  showModal.value=true;
}
function openEdit(t) {
  msg.value=""; ok.value=true;
  editingId.value=t.id;
  form.value={
    name: t.user?.name || "",
    email: t.user?.email || "",
    password: "",
    employee_no: t.employee_no || "",
    phone: t.phone || "",
    designation: t.designation || "",
  };
  showModal.value=true;
}
function close(){ showModal.value=false; }

async function load() {
  const { data } = await api.get("/teachers");
  teachers.value = data.data ?? data;
}

async function save() {
  msg.value="";
  try {
    const payload = { ...form.value };
    if (editingId.value) {
      await api.put(`/teachers/${editingId.value}`, payload);
    } else {
      await api.post("/teachers", payload);
    }
    ok.value=true; msg.value="Saved!";
    await load();
    setTimeout(() => (showModal.value=false), 400);
  } catch(e) {
    ok.value=false;
    msg.value = e?.response?.data?.message || "Save failed";
  }
}

async function remove(t) {
  if (!confirm(`Delete teacher ${t.user?.name}?`)) return;
  await api.delete(`/teachers/${t.id}`);
  await load();
}

onMounted(load);
</script>
