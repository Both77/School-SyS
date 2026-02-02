<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::orderBy('name')->get();
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string',
            'code' => 'nullable|string',
        ]);
        return response()->json(Subject::create($data), 201);
    }

    public function show(Subject $subject)
    {
        return $subject;
    }

    public function update(Request $req, Subject $subject)
    {
        $data = $req->validate([
            'name' => 'required|string',
            'code' => 'nullable|string',
        ]);
        $subject->update($data);
        return $subject->fresh();
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->noContent();
    }
}
