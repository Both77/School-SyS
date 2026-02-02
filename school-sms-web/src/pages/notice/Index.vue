<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Notices</h1>
      <button v-if="isAdmin" class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">
        + Add Notice
      </button>
    </div>

    <div class="bg-white rounded shadow divide-y">
      <div v-for="n in notices" :key="n.id" class="p-4">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="font-semibold">{{ n.title }}</div>
            <div class="text-xs text-slate-500">
              Publish: {{ n.publish_at ? new Date(n.publish_at).toLocaleString() : "draft" }}
            </div>
          </div>
          <div v-if="isAdmin" class="shrink-0">
            <button class="text-blue-700 text-sm" @click="openEdit(n)">Edit</button>
            <span class="mx-2 text-slate-300">|</span>
            <button class="text-red-700 text-sm" @click="remove(n)">Delete</button>
          </div>
        </div>
        <div class="text-sm text-slate-700 mt-2 whitespace-pre-line">{{ n.body }}</div>
      </div>

      <div v-if="notices.length===0" class="p-4 text-slate-500">No notices</div>
    </div>

    <!-- modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-lg p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit Notice" : "Add Notice" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div>
          <label class="text-xs text-slate-600">Title</label>
          <input v-model="form.title" class="w-full border rounded px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="text-xs text-slate-600">Publish at (optional)</label>
          <input type="datetime-local" v-model="form.publish_at" class="w-full border rounded px-3 py-2 text-sm" />
        </div>
        <div>
          <label class="text-xs text-slate-600">Body</label>
          <textarea v-model="form.body" class="w-full border rounded px-3 py-2 text-sm" rows="5"></textarea>
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
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();
const isAdmin = computed(() => auth.user?.role === "admin");

const notices = ref([]);
const showModal = ref(false);
const editingId = ref(null);

const form = ref({ title:"", body:"", publish_at:"" });
const msg = ref("");
const ok = ref(true);

async function load() {
  notices.value = (await api.get("/notices")).data;
}

function openCreate(){
  editingId.value=null;
  form.value={ title:"", body:"", publish_at:"" };
  msg.value=""; ok.value=true;
  showModal.value=true;
}
function openEdit(n){
  editingId.value=n.id;
  form.value={
    title: n.title,
    body: n.body,
    publish_at: n.publish_at ? new Date(n.publish_at).toISOString().slice(0,16) : ""
  };
  msg.value=""; ok.value=true;
  showModal.value=true;
}
function close(){ showModal.value=false; }

async function save(){
  msg.value="";
  try{
    const payload = {
      title: form.value.title,
      body: form.value.body,
      publish_at: form.value.publish_at ? new Date(form.value.publish_at).toISOString() : null,
    };
    if (editingId.value) await api.put(`/notices/${editingId.value}`, payload);
    else await api.post("/notices", payload);

    ok.value=true; msg.value="Saved!";
    await load();
    setTimeout(()=>showModal.value=false, 400);
  } catch(e){
    ok.value=false; msg.value = e?.response?.data?.message || "Save failed";
  }
}

async function remove(n){
  if (!confirm("Delete notice?")) return;
  await api.delete(`/notices/${n.id}`);
  await load();
}

onMounted(load);
</script>
