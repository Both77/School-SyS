<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return Student::with(['class','section'])->latest()->paginate(20);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'admission_no' => 'required|string|unique:students,admission_no',
            'name' => 'required|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'guardian_phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        return response()->json(Student::create($data), 201);
    }

    public function show(Student $student)
    {
        return $student->load(['class','section']);
    }

    public function update(Request $req, Student $student)
    {
        $data = $req->validate([
            'admission_no' => 'sometimes|string|unique:students,admission_no,' . $student->id,
            'name' => 'sometimes|string',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string',
            'class_id' => 'sometimes|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'guardian_phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        $student->update($data);
        return $student->fresh()->load(['class','section']);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->noContent();
    }
}
