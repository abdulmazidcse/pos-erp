<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'parent_cat_name' => $this->parents->name ?? 'N/A',
            'description'   => $this->description,
            'status'    => $this->status,
            'order'     => $this->order,
            'image'     => $this->image ? asset('public/uploads/product_categories/'.$this->image) : '',
            'company_id'=> $this->company_id,
            'company_name'  => $this->company->name ?? 'Default',
            'is_featured'   => $this->is_featured,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'parent_categories' => $this->parent,
            'discount' => $this->discount,
            'childs' => $this->childs,
            'company'       => $this->company
        ];
    }
}
