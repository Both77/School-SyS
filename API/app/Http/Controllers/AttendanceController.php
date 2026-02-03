<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function mark(Request $req)
    {
        $data = $req->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'items' => 'required|array|min:1',
            'items.*.student_id' => 'required|exists:students,id',
            'items.*.status' => 'required|in:present,absent,late',
        ]);

        $studentIds = collect($data['items'])->pluck('student_id')->all();
        $students = Student::whereIn('id', $studentIds)->get()->keyBy('id');

        foreach ($data['items'] as $item) {
            $s = $students[$item['student_id']] ?? null;
            if (!$s || (int)$s->class_id !== (int)$data['class_id']) {
                return response()->json(['message' => 'Student not in class'], 422);
            }
            if (!empty($data['section_id']) && (int)$s->section_id !== (int)$data['section_id']) {
                return response()->json(['message' => 'Student not in section'], 422);
            }

            Attendance::updateOrCreate(
                ['date' => $data['date'], 'student_id' => $item['student_id']],
                [
                    'class_id' => $data['class_id'],
                    'section_id' => $data['section_id'] ?? $s->section_id,
                    'status' => $item['status']
                ]
            );
        }

        return response()->json(['message' => 'Attendance saved']);
    }

    public function list(Request $req)
    {
        $data = $req->validate([
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $q = Attendance::with('student')
            ->where('date', $data['date'])
            ->where('class_id', $data['class_id']);

        if (!empty($data['section_id'])) $q->where('section_id', $data['section_id']);

        return $q->get();
    }

    public function reports(Request $req)
    {
        $data = $req->validate([
            'class_id' => 'nullable|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Get students based on filters
        $studentQuery = Student::with('class');
        if (!empty($data['class_id'])) {
            $studentQuery->where('class_id', $data['class_id']);
        }
        if (!empty($data['section_id'])) {
            $studentQuery->where('section_id', $data['section_id']);
        }
        $students = $studentQuery->get();

        // Get attendance data
        $attendanceQuery = Attendance::whereBetween('date', [$data['from_date'], $data['to_date']]);
        if (!empty($data['class_id'])) {
            $attendanceQuery->where('class_id', $data['class_id']);
        }
        if (!empty($data['section_id'])) {
            $attendanceQuery->where('section_id', $data['section_id']);
        }
        $attendances = $attendanceQuery->get()->groupBy('student_id');

        // Calculate total working days (assuming attendance is taken every day)
        $fromDate = new \DateTime($data['from_date']);
        $toDate = new \DateTime($data['to_date']);
        $totalDays = $fromDate->diff($toDate)->days + 1;

        $reports = [];
        $totalPresentDays = 0;
        $totalAbsentDays = 0;

        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());
            $presentDays = $studentAttendances->where('status', 'present')->count();
            $absentDays = $studentAttendances->where('status', 'absent')->count();
            $lateDays = $studentAttendances->where('status', 'late')->count();

            // Consider late as half present
            $effectivePresentDays = $presentDays + ($lateDays * 0.5);
            $attendancePercentage = $totalDays > 0 ? round(($effectivePresentDays / $totalDays) * 100, 1) : 0;

            $reports[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'admission_no' => $student->admission_no,
                'class_name' => $student->class->name ?? 'N/A',
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'total_days' => $totalDays,
                'attendance_percentage' => $attendancePercentage,
            ];

            $totalPresentDays += $effectivePresentDays;
            $totalAbsentDays += $absentDays;
        }

        $summary = [
            'total_students' => $students->count(),
            'total_days' => $totalDays,
            'present_days' => round($totalPresentDays, 1),
            'absent_days' => $totalAbsentDays,
            'attendance_percentage' => $students->count() > 0 ? round(($totalPresentDays / ($students->count() * $totalDays)) * 100, 1) : 0,
        ];

        return response()->json([
            'reports' => $reports,
            'summary' => $summary,
        ]);
    }
}
