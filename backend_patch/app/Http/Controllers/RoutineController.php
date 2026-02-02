<?php

namespace App\Http\Controllers;

use App\Models\Routine;
use Illuminate\Http\Request;

class RoutineController extends Controller
{
    public function index(Request $req)
    {
        $q = Routine::with(['class','section','subject','teacher.user'])->orderBy('weekday')->orderBy('start_time');

        if ($req->has('class_id')) $q->where('class_id', $req->query('class_id'));
        if ($req->has('section_id')) $q->where('section_id', $req->query('section_id'));

        return $q->get();
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room' => 'nullable|string',
        ]);
        return response()->json(Routine::create($data), 201);
    }

    public function show(Routine $routine)
    {
        return $routine->load(['class','section','subject','teacher.user']);
    }

    public function update(Request $req, Routine $routine)
    {
        $data = $req->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'weekday' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'room' => 'nullable|string',
        ]);
        $routine->update($data);
        return $routine->fresh()->load(['class','section','subject','teacher.user']);
    }

    public function destroy(Routine $routine)
    {
        $routine->delete();
        return response()->noContent();
    }
}
