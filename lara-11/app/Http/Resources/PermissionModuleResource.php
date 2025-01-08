<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionModuleResource extends JsonResource
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
            'slug'  => $this->slug,
            'parent_id' => $this->parent_id,
            'parent_module_name'    => $this->parents ? $this->parents->name : 'N/A',
            'icon_name' => $this->icon_name,
            'menu_order' => $this->menu_order,
            'is_children'   => $this->is_children,
            'is_action_menu' => $this->is_action_menu,
            'is_multiple_action' => $this->is_multiple_action,
            'total_actions' => $this->total_actions,
            'created_at'    => $this->created_at->format("Y-m-d H:i:s"),
            'updated_at'    => $this->updated_at->format("Y-m-d H:i:s"),
        ];
    }
}
