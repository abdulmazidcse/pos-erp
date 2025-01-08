<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use File;

class CompanyResource extends JsonResource
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
            'name'    => $this->name,
            'address'   => $this->address,
            'logo'  => (!empty($this->logo) && File::exists(public_path('/uploads/company/' . $this->logo))) ?  asset('/public/uploads/company/' . $this->logo) : asset('common\avater.png'),
            'contact_person_name'    => $this->contact_person_name,
            'contact_person_number'    => $this->contact_person_number,
            'status'    => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}