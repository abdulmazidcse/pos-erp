<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public static $wrap = '';
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
            'description'  => $this->description,
            'guard_name' => $this->guard_name,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'role_permissions'   => $this->permissions->map(function ($item){
                return $item;
            }),
            'total_module_permission'   => $this->permissions->groupBy('module_id')->map(function ($module) {
                return $module->count();
            }),
        ];
    }
}
