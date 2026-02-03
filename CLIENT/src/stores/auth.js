import { defineStore } from "pinia";
import { api } from "../lib/api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token"),
  }),
  actions: {
    async login(email, password) {
      const { data } = await api.post("/login", { email, password });
      this.token = data.token;
      this.user = data.user;
      localStorage.setItem("token", data.token);
    },
    async fetchMe() {
      if (!this.token) return;
      const { data } = await api.get("/me");
      this.user = data;
    },
    async logout() {
      try { await api.post("/logout"); } catch {}
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");
    },
  },
});
