<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $courseService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $courses = $this->courseService->getAllCourses($request->all());
        
        return response()->json([
            'version' => '1.0',
            'data' => CourseResource::collection($courses->items()),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
                'last_page' => $courses->lastPage(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|integer|min:1|max:6',
        ]);

        $course = $this->courseService->createCourse($validated);
        
        return response()->json([
            'version' => '1.0',
            'message' => 'Course created successfully',
            'data' => new CourseResource($course)
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $course = $this->courseService->getCourseById($id);
        
        if (!$course) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'data' => new CourseResource($course)
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'credits' => 'sometimes|integer|min:1|max:6',
        ]);

        $updated = $this->courseService->updateCourse($id, $validated);
        
        if (!$updated) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Course not found'
            ], 404);
        }

        $course = $this->courseService->getCourseById($id);

        return response()->json([
            'version' => '1.0',
            'message' => 'Course updated successfully',
            'data' => new CourseResource($course)
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->courseService->deleteCourse($id);
        
        if (!$deleted) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'message' => 'Course deleted successfully'
        ]);
    }

    public function students(int $id): JsonResponse
    {
        $students = $this->courseService->getCourseStudents($id);
        
        return response()->json([
            'version' => '1.0',
            'data' => $students
        ]);
    }
}