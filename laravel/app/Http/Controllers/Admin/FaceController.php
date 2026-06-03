<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Face;
use App\Models\Student;

class FaceController extends Controller
{
    public function index()
    {
        $students = Student::with(['classRoom', 'faces'])
            ->active()
            ->withCount(['faces' => fn($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.faces.index', compact('students'));
    }

    public function register(Student $student)
    {
        $student->load(['classRoom', 'faces']);
        return view('admin.faces.register', compact('student'));
    }

    public function destroy(Face $face)
    {
        $face->update(['is_active' => false]);

        return redirect()->back()
            ->with('success', 'Data wajah berhasil dihapus.');
    }
}
