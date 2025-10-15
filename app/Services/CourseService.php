<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseService
{
    public function getAllCourses(array $filters = []): LengthAwarePaginator
    {
        $query = Course::with('students');

        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (isset($filters['min_credits'])) {
            $query->where('credits', '>=', $filters['min_credits']);
        }

        return $query->paginate(10);
    }

    public function getCourseById(int $id): ?Course
    {
        return Course::with('students')->find($id);
    }

    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(int $id, array $data): bool
    {
        $course = Course::find($id);
        
        if (!$course) {
            return false;
        }

        return $course->update($data);
    }

    public function deleteCourse(int $id): bool
    {
        $course = Course::find($id);
        
        if (!$course) {
            return false;
        }

        return $course->delete();
    }

    public function getCourseStudents(int $courseId): array
    {
        $course = Course::with('students')->find($courseId);
        
        return $course ? $course->students->toArray() : [];
    }
}