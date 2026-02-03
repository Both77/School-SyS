<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-slate-200 p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
      <div class="p-8">
        
        <!-- Header Section -->
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Welcome Back</h1>
          <p class="text-sm text-gray-500 mt-2">Sign in to access your dashboard</p>
        </div>

        <form class="space-y-6" @submit.prevent="submit">
          
          <!-- Email Field -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <!-- Mail Icon -->
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
              </div>
              <input 
                v-model="email" 
                type="email" 
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all duration-200 text-gray-900 placeholder-gray-400"
                placeholder="name@school.com" 
              />
            </div>
          </div>

          <!-- Password Field -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="block text-sm font-medium text-gray-700">Password</label>
              <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 hover:underline">Forgot?</a>
            </div>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <!-- Lock Icon -->
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors duration-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
              </div>
              <input 
                v-model="password" 
                type="password" 
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 outline-none transition-all duration-200 text-gray-900 placeholder-gray-400"
                placeholder="••••••••" 
              />
            </div>
          </div>

          <!-- Action Button -->
          <button 
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
          >
            Sign In
          </button>
        </form>

        <!-- Error Message -->
        <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-100 rounded-lg flex items-start">
           <svg class="h-5 w-5 text-red-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
           <p class="text-sm text-red-600">{{ error }}</p>
        </div>

        <!-- Footer / Demo Credentials -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
          <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Demo Credentials</p>
          <div class="inline-flex items-center space-x-2 bg-slate-50 px-3 py-1 rounded-full border border-slate-200">
             <span class="text-xs font-mono text-slate-600">admin@school.com</span>
             <span class="text-slate-300">|</span>
             <span class="text-xs font-mono text-slate-600">admin123</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const email = ref("admin@school.com");
const password = ref("admin123");
const error = ref("");

const auth = useAuthStore();
const router = useRouter();

async function submit() {
  error.value = "";
  try {
    await auth.login(email.value, password.value);
    router.push("/");
  } catch (e) {
    error.value = e?.response?.data?.message || "Login failed. Please check your credentials.";
  }
}
</script>