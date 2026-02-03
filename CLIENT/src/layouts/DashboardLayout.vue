<template>
  <div class="min-h-screen bg-gray-50 flex font-sans">
    <!-- Sidebar Component -->
    <Sidebar />

    <!-- Main Layout -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
      
      <!-- Top Navigation Header -->
      <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-10">
        
        <!-- Brand / Title -->
        <div class="flex items-center gap-3">
          <div class="bg-indigo-600 text-white p-1.5 rounded-lg">
             <!-- Simple logo icon placeholder -->
             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <span class="font-bold text-gray-800 text-lg tracking-tight">School SMS</span>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-6">
          
          <!-- User Profile -->
          <div class="flex items-center gap-3 pl-6 border-l border-gray-100">
            <div class="hidden md:block text-right leading-tight">
              <div class="text-sm font-semibold text-gray-900">{{ auth.user?.name || 'User' }}</div>
              <div class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full inline-block mt-0.5">
                {{ auth.user?.role || 'Guest' }}
              </div>
            </div>
            
            <!-- Avatar Placeholder -->
            <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-md border-2 border-white text-sm">
              {{ (auth.user?.name?.[0] || 'U').toUpperCase() }}
            </div>
          </div>

          <!-- Logout Button -->
          <button 
            @click="logout"
            class="group flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
            title="Sign out"
          >
            <span class="hidden sm:inline">Logout</span>
            <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
          </button>
        </div>
      </header>

      <!-- Page Content Wrapper -->
      <div class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
        <div class="max-w-7xl mx-auto h-full">
          <!-- Router View with Fade Transition -->
          <router-view v-slot="{ Component }">
            <transition 
              enter-active-class="transition ease-out duration-200"
              enter-from-class="opacity-0 translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition ease-in duration-150"
              leave-from-class="opacity-100 translate-y-0"
              leave-to-class="opacity-0 translate-y-1"
              mode="out-in"
            >
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </div>

    </main>
  </div>
</template>

<script setup>
import Sidebar from "../components/Sidebar.vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

async function logout() {
  await auth.logout();
  router.push("/login");
}
</script>