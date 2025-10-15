<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

class EnrollmentService
{
    public function getAllEnrollments(array $filters = []): LengthAwarePaginator
    {
        $query = Enrollment::with(['student', 'course']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        return $query->paginate(10);
    }

    public function getEnrollmentById(int $id): ?Enrollment
    {
        return Enrollment::with(['student', 'course'])->find($id);
    }

    public function enrollStudent(array $data): ?Enrollment
    {
        // Check if student exists
        $student = Student::find($data['student_id']);
        if (!$student) {
            throw new \InvalidArgumentException('Student not found');
        }

        // Check if course exists
        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \InvalidArgumentException('Course not found');
        }

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', $data['student_id'])
            ->where('course_id', $data['course_id'])
            ->first();

        if ($existingEnrollment) {
            throw new \InvalidArgumentException('Student is already enrolled in this course');
        }

        return Enrollment::create([
            'student_id' => $data['student_id'],
            'course_id' => $data['course_id'],
            'enrolled_at' => now(),
            'status' => 'active',
        ]);
    }

    public function updateEnrollmentStatus(int $id, string $status): bool
    {
        $enrollment = Enrollment::find($id);
        
        if (!$enrollment) {
            return false;
        }

        if (!in_array($status, ['active', 'completed', 'dropped'])) {
            throw new \InvalidArgumentException('Invalid status');
        }

        return $enrollment->update(['status' => $status]);
    }

    public function dropEnrollment(int $id): bool
    {
        $enrollment = Enrollment::find($id);
        
        if (!$enrollment) {
            return false;
        }

        return $enrollment->delete();
    }

    public function getStudentEnrollments(int $studentId): array
    {
        return Enrollment::with('course')
            ->where('student_id', $studentId)
            ->get()
            ->toArray();
    }

    public function getCourseEnrollments(int $courseId): array
    {
        return Enrollment::with('student')
            ->where('course_id', $courseId)
            ->get()
            ->toArray();
    }
}