<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'company_id'    => $this->company_id,
            'company_name'  => $this->company->name ?? 'Default',
            'status'    => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
