<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'module_id' => $this->module_id,
            'module_name'=> $this->permission_modules->name,
            'module_slug'=> $this->permission_modules->slug,
            'name'  => $this->name,
            'slug'  => $this->slug,
            'url_path'  => $this->url_path,
            'component_path'  => $this->component_path,
            'is_nav'  => $this->is_nav,
            'backend_url'  => $this->backend_url,
            'frontend_url'  => $this->frontend_url,
            'column_status' => $this->column_status,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
