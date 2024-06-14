<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'priority_id' => $this->priority_id,
            'leader_id' => $this->leader_id,            
            'name' => $this->name,            
            'status' => $this->status,
            'logo' => $this->logo,
            'fileUrl' => $this->fileUrl,
            'user' => new UserResource($this->whenLoaded('user')),  
            'leader' => new UserResource($this->whenLoaded('leader')),
            'programmers' => UserResource::collection($this->whenLoaded('programmers')),
            'programmerIds' => array_map(
                function ($programmer) {
                    return $programmer['id'];
                },
                $this->programmers->toArray()
            ),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'createdAt' => $this->created_at,       
            'updatedAt' => $this->updated_at,
        ];
    }
}
