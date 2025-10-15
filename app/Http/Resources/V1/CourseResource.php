<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'credits' => $this->credits,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'enrollments' => EnrollmentResource::collection($this->whenLoaded('enrollments')),
            'students' => StudentResource::collection($this->whenLoaded('students')),
        ];
    }
}