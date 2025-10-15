<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth->format('Y-m-d'),
            'age' => $this->date_of_birth->age,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}