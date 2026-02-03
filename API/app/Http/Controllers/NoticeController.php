<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index()
    {
        return Notice::orderByDesc('publish_at')->orderByDesc('id')->get();
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'publish_at' => 'nullable|date',
        ]);
        return response()->json(Notice::create($data), 201);
    }

    public function show(Notice $notice)
    {
        return $notice;
    }

    public function update(Request $req, Notice $notice)
    {
        $data = $req->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'publish_at' => 'nullable|date',
        ]);
        $notice->update($data);
        return $notice->fresh();
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return response()->noContent();
    }
}
