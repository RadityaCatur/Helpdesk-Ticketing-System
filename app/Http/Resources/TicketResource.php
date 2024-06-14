<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'subject' => $this->subject,
            'description' => $this->description,
            'files' => $this->files,
            'duration' => $this->duration,
            'remainingTime' => $this->remainingTime,
            'status' => $this->status,
            'response' => $this->response,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'project' => new ProjectResource($this->whenLoaded('project')),       
            'room' => $this->room,
        ];
    }
}
