<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EnrollmentResource;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $enrollmentService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $enrollments = $this->enrollmentService->getAllEnrollments($request->all());
        
        return response()->json([
            'version' => '1.0',
            'data' => EnrollmentResource::collection($enrollments->items()),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
                'last_page' => $enrollments->lastPage(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        try {
            $enrollment = $this->enrollmentService->enrollStudent($validated);
            
            return response()->json([
                'version' => '1.0',
                'message' => 'Student enrolled successfully',
                'data' => new EnrollmentResource($enrollment)
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'version' => '1.0',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(int $id): JsonResponse
    {
        $enrollment = $this->enrollmentService->getEnrollmentById($id);
        
        if (!$enrollment) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Enrollment not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'data' => new EnrollmentResource($enrollment)
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,completed,dropped',
        ]);

        try {
            $updated = $this->enrollmentService->updateEnrollmentStatus($id, $validated['status']);
            
            if (!$updated) {
                return response()->json([
                    'version' => '1.0',
                    'error' => 'Enrollment not found'
                ], 404);
            }

            $enrollment = $this->enrollmentService->getEnrollmentById($id);

            return response()->json([
                'version' => '1.0',
                'message' => 'Enrollment status updated successfully',
                'data' => new EnrollmentResource($enrollment)
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'version' => '1.0',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->enrollmentService->dropEnrollment($id);
        
        if (!$deleted) {
            return response()->json([
                'version' => '1.0',
                'error' => 'Enrollment not found'
            ], 404);
        }

        return response()->json([
            'version' => '1.0',
            'message' => 'Enrollment dropped successfully'
        ]);
    }

    public function studentEnrollments(int $studentId): JsonResponse
    {
        $enrollments = $this->enrollmentService->getStudentEnrollments($studentId);
        
        return response()->json([
            'version' => '1.0',
            'data' => EnrollmentResource::collection($enrollments)
        ]);
    }

    public function courseEnrollments(int $courseId): JsonResponse
    {
        $enrollments = $this->enrollmentService->getCourseEnrollments($courseId);
        
        return response()->json([
            'version' => '1.0',
            'data' => EnrollmentResource::collection($enrollments)
        ]);
    }
}