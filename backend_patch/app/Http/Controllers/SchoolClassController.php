<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        return SchoolClass::with('sections')->orderBy('name')->get();
    }

    public function store(Request $req)
    {
        $data = $req->validate(['name' => 'required|string']);
        SchoolClass::create($data);
        return response()->json()->setStatusCode(201);
    }

    public function show(SchoolClass $class)
    {
        return $class->load('sections');
    }

    public function update(Request $req, SchoolClass $class)
    {
        $data = $req->validate(['name' => 'required|string']);
        $class->update($data);
        return $class->fresh();
    }

    public function destroy(SchoolClass $class)
    {
        $class->delete();
        return response()->noContent();
    }
}
