<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Sections</h1>
      <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">+ Add Section</button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Class</th>
            <th class="text-left p-3">Section</th>
            <th class="text-left p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="s in sections" :key="s.id" class="border-t">
            <td class="p-3">{{ s.class?.name }}</td>
            <td class="p-3">{{ s.name }}</td>
            <td class="p-3">
              <button class="text-blue-700" @click="openEdit(s)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(s)">Delete</button>
            </td>
          </tr>
          <tr v-if="sections.length===0">
            <td class="p-4 text-slate-500" colspan="3">No sections</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-md p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Section" : "Add Section" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div>
          <label class="text-xs text-slate-600">Class</label>
          <select v-model.number="class_id" class="w-full border rounded px-3 py-2 text-sm">
            <option disabled :value="0">Select class</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-slate-600">Section Name</label>
          <input v-model="name" class="w-full border rounded px-3 py-2 text-sm" placeholder="A" />
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
const sections = ref([]);

const showModal = ref(false);
const editingId = ref(null);
const class_id = ref(0);
const name = ref("");

const msg = ref("");
const ok = ref(true);

function openCreate(){
  editingId.value=null;
  class_id.value=classes.value[0]?.id || 0;
  name.value="";
  msg.value=""; ok.value=true;
  showModal.value=true;
}
function openEdit(s){
  editingId.value=s.id;
  class_id.value=s.class_id;
  name.value=s.name;
  msg.value=""; ok.value=true;
  showModal.value=true;
}
function close(){ showModal.value=false; }

async function load(){
  classes.value = (await api.get("/classes")).data;
  sections.value = (await api.get("/sections")).data;
}
async function save(){
  msg.value="";
  try{
    const payload = { class_id: class_id.value, name: name.value };
    if (editingId.value) await api.put(`/sections/${editingId.value}`, payload);
    else await api.post("/sections", payload);
    ok.value=true; msg.value="Saved!";
    await load();
    setTimeout(()=>showModal.value=false, 400);
  } catch(e){
    ok.value=false; msg.value = e?.response?.data?.message || "Save failed";
  }
}
async function remove(s){
  if (!confirm(`Delete section ${s.name}?`)) return;
  await api.delete(`/sections/${s.id}`);
  await load();
}

onMounted(load);
</script>
