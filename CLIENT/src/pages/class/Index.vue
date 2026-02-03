<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Classes</h1>
      <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">+ Add Class</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Name</th>
            <th class="text-left p-3">Sections</th>
            <th class="text-left p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in classes" :key="c.id" class="border-t">
            <td class="p-3">{{ c.name }}</td>
            <td class="p-3">
              <span class="text-slate-600">{{ (c.sections||[]).map(s=>s.name).join(", ") || "-" }}</span>
            </td>
            <td class="p-3">
              <button class="text-blue-700" @click="openEdit(c)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(c)">Delete</button>
            </td>
          </tr>
          <tr v-if="classes.length===0">
            <td class="p-4 text-slate-500" colspan="3">No classes</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-md p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Class" : "Add Class" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div>
          <label class="text-xs text-slate-600">Name</label>
          <input v-model="name" class="w-full border rounded px-3 py-2 text-sm" />
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
import { ref, onMounted } from "vue";
import { api } from "@lib/api";

const classes = ref([]);
const showModal = ref(false);
const editingId = ref(null);
const name = ref("");
const msg = ref("");
const ok = ref(true);

function openCreate(){ editingId.value=null; name.value=""; msg.value=""; ok.value=true; showModal.value=true; }
function openEdit(c){ editingId.value=c.id; name.value=c.name; msg.value=""; ok.value=true; showModal.value=true; }
function close(){ showModal.value=false; }

async function load() {
  const { data } = await api.get("/classes");
  classes.value = data;
}
async function save() {
  msg.value="";
  try {
    if (editingId.value) await api.put(`/classes/${editingId.value}`, { name: name.value });
    else await api.post("/classes", { name: name.value });
    ok.value=true; msg.value="Saved!";
    await load();
    setTimeout(()=>showModal.value=false, 400);
  } catch(e) {
    ok.value=false; msg.value = e?.response?.data?.message || "Save failed";
  }
}
async function remove(c){
  if (!confirm(`Delete class ${c.name}?`)) return;
  await api.delete(`/classes/${c.id}`);
  await load();
}

onMounted(load);
</script>
