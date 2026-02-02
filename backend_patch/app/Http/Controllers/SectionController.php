<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return Section::with('class')->orderBy('name')->get();
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string',
        ]);
        return response()->json(Section::create($data), 201);
    }

    public function show(Section $section)
    {
        return $section->load('class');
    }

    public function update(Request $req, Section $section)
    {
        $data = $req->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string',
        ]);
        $section->update($data);
        return $section->fresh()->load('class');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return response()->noContent();
    }
}
