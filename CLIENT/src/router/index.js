import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

const routes = [
  { path: "/login", component: () => import("../pages/Login.vue") },
  {
    path: "/",
    component: () => import("../layouts/DashboardLayout.vue"),
    children: [
      { path: "", component: () => import("../pages/Dashboard.vue") },

      // Shared
      { path: "students", component: () => import("../pages/students/Index.vue"), meta: { roles: ["admin","teacher"] } },
      { path: "attendance", component: () => import("../pages/attendance/Mark.vue"), meta: { roles: ["admin","teacher"] } },
      { path: "attendance-reports", component: () => import("../pages/reports/AttendanceReports.vue"), meta: { roles: ["admin","teacher"] } },
      { path: "class-routine", component: () => import("../pages/routine/Index.vue"), meta: { roles: ["admin","teacher"] } },
      { path: "notice", component: () => import("../pages/notice/Index.vue"), meta: { roles: ["admin","teacher"] } },

      // Admin only
      { path: "users", component: () => import("../pages/users/Index.vue"), meta: { roles: ["admin"] } },
      { path: "teachers", component: () => import("../pages/teachers/Index.vue"), meta: { roles: ["admin"] } },
      { path: "class", component: () => import("../pages/class/Index.vue"), meta: { roles: ["admin"] } },
      { path: "section", component: () => import("../pages/section/Index.vue"), meta: { roles: ["admin"] } },
      { path: "subject", component: () => import("../pages/subject/Index.vue"), meta: { roles: ["admin"] } },
      { path: "routines-admin", component: () => import("../pages/routine/AdminCrud.vue"), meta: { roles: ["admin"] } },
    ],
  },
];

const router = createRouter({ history: createWebHistory(), routes });

router.beforeEach(async (to) => {
  const auth = useAuthStore();

  if (!auth.user && auth.token) {
    try { await auth.fetchMe(); } catch {}
  }

  if (!auth.token && to.path !== "/login") return "/login";
  if (auth.token && to.path === "/login") return "/";

  const roles = to.meta?.roles;
  if (roles && auth.user && !roles.includes(auth.user.role)) return "/";

  return true;
});

export default router;
