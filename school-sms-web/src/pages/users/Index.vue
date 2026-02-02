<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">User Management</h1>
      <button class="px-3 py-2 rounded bg-slate-900 text-white text-sm" @click="openCreate">
        + Add User
      </button>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Name</th>
            <th class="text-left p-3">Email</th>
            <th class="text-left p-3">Role</th>
            <th class="text-left p-3">Created</th>
            <th class="text-left p-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-t">
            <td class="p-3">{{ user.name }}</td>
            <td class="p-3">{{ user.email }}</td>
            <td class="p-3">
              <span :class="user.role === 'admin' ? 'text-red-600 font-semibold' : 'text-blue-600'">
                {{ user.role }}
              </span>
            </td>
            <td class="p-3">{{ formatDate(user.created_at) }}</td>
            <td class="p-3">
              <button class="text-blue-700" @click="openEdit(user)">Edit</button>
              <span class="mx-2 text-slate-300">|</span>
              <button class="text-red-700" @click="remove(user)">Delete</button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td class="p-4 text-slate-500" :colspan="5">No users found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4">
      <div class="bg-white rounded shadow w-full max-w-lg p-4 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold">{{ editingId ? "Edit User" : "Add User" }}</h2>
          <button class="text-slate-600" @click="close">✕</button>
        </div>

        <div class="grid gap-3">
          <div>
            <label class="text-xs text-slate-600">Name</label>
            <input v-model="form.name" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Email</label>
            <input v-model="form.email" type="email" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Password {{ editingId ? '(leave blank to keep current)' : '' }}</label>
            <input v-model="form.password" type="password" class="w-full border rounded px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-xs text-slate-600">Role</label>
            <select v-model="form.role" class="w-full border rounded px-3 py-2 text-sm">
              <option value="teacher">Teacher</option>
              <option value="admin">Admin</option>
            </select>
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

const users = ref([]);
const showModal = ref(false);
const editingId = ref(null);
const form = ref({
  name: "",
  email: "",
  password: "",
  role: "teacher",
});
const msg = ref("");
const ok = ref(true);

function resetForm() {
  form.value = {
    name: "",
    email: "",
    password: "",
    role: "teacher",
  };
}

function openCreate() {
  msg.value = "";
  ok.value = true;
  editingId.value = null;
  resetForm();
  showModal.value = true;
}

function openEdit(user) {
  msg.value = "";
  ok.value = true;
  editingId.value = user.id;
  form.value = {
    name: user.name,
    email: user.email,
    password: "",
    role: user.role,
  };
  showModal.value = true;
}

function close() { showModal.value = false; }

async function load() {
  const { data } = await api.get("/users");
  users.value = data.data ?? data;
}

async function save() {
  msg.value = "";
  try {
    const payload = { ...form.value };
    if (!payload.password) delete payload.password;

    if (editingId.value) {
      await api.put(`/users/${editingId.value}`, payload);
    } else {
      await api.post("/users", payload);
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

async function remove(user) {
  if (!confirm(`Delete user ${user.name}?`)) return;
  try {
    await api.delete(`/users/${user.id}`);
    await load();
  } catch (e) {
    alert(e?.response?.data?.message || "Delete failed");
  }
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString();
}

onMounted(async () => {
  await load();
});
</script>