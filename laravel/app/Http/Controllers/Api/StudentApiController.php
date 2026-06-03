<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;

class StudentApiController extends Controller
{
    public function index(): JsonResponse
    {
        $students = Student::with('classRoom')
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'nim'        => $s->nim,
                'name'       => $s->name,
                'class'      => $s->classRoom->name ?? '-',
                'has_face'   => $s->hasFaceRegistered(),
                'photo_url'  => $s->photo_url,
            ]);

        return response()->json(['success' => true, 'data' => $students]);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('classRoom', 'faces');
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $student = Student::create($request->validated());
        return response()->json(['success' => true, 'data' => $student], 201);
    }

    public function update(StoreStudentRequest $request, Student $student): JsonResponse
    {
        $student->update($request->validated());
        return response()->json(['success' => true, 'data' => $student]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();
        return response()->json(['success' => true, 'message' => 'Mahasiswa dihapus.']);
    }
}
