<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        return Teacher::with('user')->latest()->paginate(20);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'employee_no' => 'required|string|unique:teachers,employee_no',
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => 'teacher',
            'password' => bcrypt($data['password']),
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'employee_no' => $data['employee_no'],
            'phone' => $data['phone'] ?? null,
            'designation' => $data['designation'] ?? null,
        ]);

        return response()->json($teacher->load('user'), 201);
    }

    public function show(Teacher $teacher)
    {
        return $teacher->load('user');
    }

    public function update(Request $req, Teacher $teacher)
    {
        $data = $req->validate([
            'employee_no' => 'sometimes|string|unique:teachers,employee_no,' . $teacher->id,
            'phone' => 'nullable|string',
            'designation' => 'nullable|string',
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:6',
        ]);

        $teacher->update($req->only(['employee_no', 'phone', 'designation']));

        $user = $teacher->user;
        $user->fill($req->only(['name', 'email']));
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->save();

        return $teacher->fresh()->load('user');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->user()->delete();
        return response()->noContent();
    }
}
