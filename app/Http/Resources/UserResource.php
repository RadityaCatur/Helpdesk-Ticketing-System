<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'fullname' => $this->fullname,
            'username' => $this->username,
            'email' => $this->email,
            'alamat' => $this->alamat,
            'handphone' => $this->handphone,
            'company_id' => $this->company_id,
            'position_id' => $this->position_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'leaderProjects' => ProjectResource::collection($this->whenLoaded('leadProjects')),
            'roles' => array_map(
                function ($role) {
                    return $role['name'];
                },
                $this->roles->toArray()
            ),
            'permissions' => array_map(
                function ($permission) {
                    return $permission['name'];
                },
                $this->getAllPermissions()->toArray()
            ),
            'openTicketCount' => $this->tickets->where('status', 'open')->count(),
            'closedTicketCount' => $this->tickets->where('status', 'closed')->count(),
            'onProgressTicketCount' => $this->tickets->where('status', 'onProgress')->count(),
            'projectCount' => $this->projects->count(),
        ];
    }
}
