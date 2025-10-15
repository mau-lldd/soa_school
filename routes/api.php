<?php
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'version' => '1.0',
            'status' => 'OK',
            'timestamp' => now()->toISOString(),
        ]);
    });

    // Student routes
    Route::apiResource('students', StudentController::class);
    Route::get('students/{id}/courses', [StudentController::class, 'courses']);

    // Course routes
    Route::apiResource('courses', CourseController::class);
    Route::get('courses/{id}/students', [CourseController::class, 'students']);

    // Enrollment routes
    Route::apiResource('enrollments', EnrollmentController::class)->except(['update']);
    Route::put('enrollments/{id}/status', [EnrollmentController::class, 'updateStatus']);
    Route::get('students/{studentId}/enrollments', [EnrollmentController::class, 'studentEnrollments']);
    Route::get('courses/{courseId}/enrollments', [EnrollmentController::class, 'courseEnrollments']);
});

// Default version redirect (optional)
Route::get('/', function () {
    return response()->json([
        'message' => 'Student Management API',
        'versions' => [
            'v1' => url('/api/v1'),
        ],
        'documentation' => 'Add /v1/ to your API endpoints',
    ]);
});

// Fallback for undefined API routes
Route::fallback(function () {
    return response()->json([
        'error' => 'API endpoint not found',
        'available_versions' => ['v1'],
        'current_version_url' => url('/api/v1'),
    ], 404);
});