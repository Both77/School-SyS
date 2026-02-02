<template>
  <aside class="w-64 min-h-screen bg-slate-900 text-slate-100">
    <div class="px-4 py-4 font-semibold border-b border-slate-800">Dashboard</div>

    <nav class="p-2 space-y-1">
      <SidebarItem v-for="m in filteredMenu" :key="m.to" :to="m.to" :icon="m.icon" :label="m.label" />
    </nav>
  </aside>
</template>

<script setup>
import SidebarItem from "./SidebarItem.vue";
import { computed } from "vue";
import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

const menu = [
  { label: "Dashboard", to: "/", icon: "📊", roles: ["admin","teacher"] },
  { label: "Students", to: "/students", icon: "👥", roles: ["admin","teacher"] },
  { label: "Users", to: "/users", icon: "👤", roles: ["admin"] },
  { label: "Teachers", to: "/teachers", icon: "🧑‍🏫", roles: ["admin"] },
  { label: "Class", to: "/class", icon: "🏫", roles: ["admin"] },
  { label: "Section", to: "/section", icon: "🧩", roles: ["admin"] },
  { label: "Subject", to: "/subject", icon: "📘", roles: ["admin"] },
  { label: "Routine (View)", to: "/class-routine", icon: "🗓️", roles: ["admin","teacher"] },
  { label: "Routine (Admin)", to: "/routines-admin", icon: "🛠️", roles: ["admin"] },
  { label: "Attendance", to: "/attendance", icon: "✅", roles: ["admin","teacher"] },
  { label: "Attendance Reports", to: "/attendance-reports", icon: "📊", roles: ["admin","teacher"] },
  { label: "Notice", to: "/notice", icon: "📢", roles: ["admin","teacher"] },
];

const filteredMenu = computed(() => {
  const role = auth.user?.role;
  return menu.filter(m => role && m.roles.includes(role));
});
</script>
