<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentService
{
    public function getAllStudents(array $filters = []): LengthAwarePaginator
    {
        $query = Student::with('courses');

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }

        return $query->paginate(10);
    }

    public function getStudentById(int $id): ?Student
    {
        return Student::with('courses')->find($id);
    }

    public function createStudent(array $data): Student
    {
        return Student::create($data);
    }

    public function updateStudent(int $id, array $data): bool
    {
        $student = Student::find($id);
        
        if (!$student) {
            return false;
        }

        return $student->update($data);
    }

    public function deleteStudent(int $id): bool
    {
        $student = Student::find($id);
        
        if (!$student) {
            return false;
        }

        return $student->delete();
    }

    public function getStudentCourses(int $studentId): array
    {
        $student = Student::with('courses')->find($studentId);
        
        return $student ? $student->courses->toArray() : [];
    }
}