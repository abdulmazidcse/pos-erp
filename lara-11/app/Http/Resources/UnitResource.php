<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'unit_code' => $this->unit_code,
            'unit_name' => $this->unit_name,
            'base_unit' => $this->base_unit,
            'base_unit_name' => $this->baseUnit ? $this->baseUnit->unit_name .' ('.$this->baseUnit->unit_code.')' : 'N/A',
            'operator'  => $this->operator,
            'operation_value'   => $this->operation_value,
            'status'   => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
