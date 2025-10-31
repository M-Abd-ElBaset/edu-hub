<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'difficulty'    => $this->difficulty,
            'instructor'    => [
                'id'    => $this->instructor->id,
                'name'  => $this->instructor->name,
            ],
            'category'      => $this->category ? ['id' => $this->category->id, 'name' => $this->category->name] : null,
            'is_published'  => $this->is_published,
            'price'         => (float) $this->price,
            'created_at'    => $this->created_at,
        ];
    }
}
