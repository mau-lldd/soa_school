<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\StudentResource;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private StudentService $studentService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $students = $this->studentService->getAllStudents($request->all());
        
        return response()->json([
            'version' => '1.0',
            'data' => StudentResource::collection($students->items()),
            'meta' => [
                'current_page' => $students->currentPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
                'last_page' => $students->lastPage(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'date_of_birth' => 'required|date|before:today',
        ]);

        $student = $this->studentService->createStudent($validated);
        
        return response()->json([
            'version' => '1.0',
            'message' => 'Student created successfully',
            'data' => new StudentResource($student)
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $student = $this->studentService->getStudentById($id);
        
        if (!$student) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'data' => new StudentResource($student)
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $id,
            'date_of_birth' => 'sometimes|date|before:today',
        ]);

        $updated = $this->studentService->updateStudent($id, $validated);
        
        if (!$updated) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Student not found'
            ], 404);
        }

        $student = $this->studentService->getStudentById($id);

        return response()->json([
            'version' => '1.0',
            'message' => 'Student updated successfully',
            'data' => new StudentResource($student)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->studentService->deleteStudent($id);
        
        if (!$deleted) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'message' => 'Student deleted successfully'
        ]);
    }

    public function courses(int $id): JsonResponse
    {
        $courses = $this->studentService->getStudentCourses($id);
        
        return response()->json([
            'version' => '1.0',
            'data' => $courses
        ]);
    }
}