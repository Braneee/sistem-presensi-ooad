<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $students = Student::with('classRoom')
            ->withCount('faces')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('nim', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhereHas('classRoom', function ($classQuery) use ($search) {
                            $classQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.index', compact('students', 'search'));
    }

    public function create()
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(StoreStudentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')
                ->store('students', 'public');
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Student $student)
    {
        $student->load(['classRoom', 'faces', 'attendances.session']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
