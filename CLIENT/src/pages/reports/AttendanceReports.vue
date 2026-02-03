<template>
  <div class="p-6 space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold">Attendance Reports</h1>
      <button class="px-3 py-2 rounded bg-white border text-sm" @click="loadReports">Refresh</button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded shadow p-4 grid gap-3 md:grid-cols-4">
      <div>
        <label class="text-xs text-slate-600">Class</label>
        <select v-model.number="filters.class_id" class="w-full border rounded px-3 py-2 text-sm" @change="loadReports">
          <option :value="0">All Classes</option>
          <option v-for="c in masters.classes" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-600">Section</label>
        <select v-model.number="filters.section_id" class="w-full border rounded px-3 py-2 text-sm" @change="loadReports">
          <option :value="0">All Sections</option>
          <option v-for="s in sectionOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs text-slate-600">From Date</label>
        <input type="date" v-model="filters.from_date" class="w-full border rounded px-3 py-2 text-sm" @change="loadReports">
      </div>
      <div>
        <label class="text-xs text-slate-600">To Date</label>
        <input type="date" v-model="filters.to_date" class="w-full border rounded px-3 py-2 text-sm" @change="loadReports">
      </div>
    </div>

    <!-- Summary Stats -->
    <div v-if="summary" class="bg-white rounded shadow p-4">
      <h2 class="text-lg font-semibold mb-4">Summary</h2>
      <div class="grid gap-4 md:grid-cols-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600">{{ summary.total_students }}</div>
          <div class="text-sm text-gray-600">Total Students</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ summary.present_days }}</div>
          <div class="text-sm text-gray-600">Present Days</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-red-600">{{ summary.absent_days }}</div>
          <div class="text-sm text-gray-600">Absent Days</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-600">{{ summary.attendance_percentage }}%</div>
          <div class="text-sm text-gray-600">Average Attendance</div>
        </div>
      </div>
    </div>

    <!-- Student-wise Report -->
    <div class="bg-white rounded shadow overflow-hidden">
      <div class="p-4 border-b">
        <h2 class="text-lg font-semibold">Student-wise Attendance</h2>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Student Name</th>
            <th class="text-left p-3">Admission No</th>
            <th class="text-left p-3">Class</th>
            <th class="text-left p-3">Present Days</th>
            <th class="text-left p-3">Total Days</th>
            <th class="text-left p-3">Attendance %</th>
            <th class="text-left p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="report in reports" :key="report.student_id" class="border-t">
            <td class="p-3">{{ report.student_name }}</td>
            <td class="p-3">{{ report.admission_no }}</td>
            <td class="p-3">{{ report.class_name }}</td>
            <td class="p-3">{{ report.present_days }}</td>
            <td class="p-3">{{ report.total_days }}</td>
            <td class="p-3">
              <span :class="getPercentageClass(report.attendance_percentage)">
                {{ report.attendance_percentage }}%
              </span>
            </td>
            <td class="p-3">
              <span :class="getStatusClass(report.attendance_percentage)">
                {{ getStatusText(report.attendance_percentage) }}
              </span>
            </td>
          </tr>
          <tr v-if="reports.length === 0">
            <td class="p-4 text-slate-500" :colspan="7">No attendance data found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Export Button -->
    <div class="flex justify-end">
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
        @click="exportReport"
      >
        Export Report
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { api } from "@lib/api";
import { fetchMasters } from "@lib/masters";

const masters = ref({ classes: [], sections: [], subjects: [] });
const reports = ref([]);
const summary = ref(null);

const filters = ref({
  class_id: 0,
  section_id: 0,
  from_date: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 days ago
  to_date: new Date().toISOString().split('T')[0], // today
});

const sectionOptions = computed(() => {
  if (!filters.value.class_id) return masters.value.sections;
  return masters.value.sections.filter(s => s.class_id === filters.value.class_id);
});

function getPercentageClass(percentage) {
  if (percentage >= 90) return 'text-green-600 font-semibold';
  if (percentage >= 75) return 'text-yellow-600 font-semibold';
  return 'text-red-600 font-semibold';
}

function getStatusClass(percentage) {
  if (percentage >= 90) return 'text-green-600';
  if (percentage >= 75) return 'text-yellow-600';
  return 'text-red-600';
}

function getStatusText(percentage) {
  if (percentage >= 90) return 'Excellent';
  if (percentage >= 75) return 'Good';
  return 'Needs Attention';
}

async function loadReports() {
  try {
    const params = new URLSearchParams();
    if (filters.value.class_id) params.append('class_id', filters.value.class_id);
    if (filters.value.section_id) params.append('section_id', filters.value.section_id);
    if (filters.value.from_date) params.append('from_date', filters.value.from_date);
    if (filters.value.to_date) params.append('to_date', filters.value.to_date);

    const { data } = await api.get(`/attendance/reports?${params}`);
    reports.value = data.reports || [];
    summary.value = data.summary || null;
  } catch (error) {
    console.error('Failed to load reports:', error);
    reports.value = [];
    summary.value = null;
  }
}

function exportReport() {
  // Simple CSV export
  if (reports.value.length === 0) {
    alert('No data to export');
    return;
  }

  const headers = ['Student Name', 'Admission No', 'Class', 'Present Days', 'Total Days', 'Attendance %', 'Status'];
  const csvContent = [
    headers.join(','),
    ...reports.value.map(report => [
      `"${report.student_name}"`,
      `"${report.admission_no}"`,
      `"${report.class_name}"`,
      report.present_days,
      report.total_days,
      `${report.attendance_percentage}%`,
      getStatusText(report.attendance_percentage)
    ].join(','))
  ].join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `attendance-report-${filters.value.from_date}-to-${filters.value.to_date}.csv`;
  a.click();
  window.URL.revokeObjectURL(url);
}

onMounted(async () => {
  masters.value = await fetchMasters().catch(() => ({ classes: [], sections: [], subjects: [] }));
  await loadReports();
});
</script>