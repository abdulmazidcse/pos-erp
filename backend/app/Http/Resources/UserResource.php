<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Str ;
use Storage ;
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
            "id" => $this->id , 
            "name" => $this->name , 
            "email" => $this->email , 
            "phone" => $this->phone ,
            "user_code" => $this->user_code , 
            "created_at" => $this->created_at , 
            "updated_at" => $this->updated_at ,
            "profile_image" => $this->profile_image,
            "company_id" => $this->company_id,
            "company_name"  => $this->company->name ?? "Default",
            "outlet_id" => $this->outlet_id,
            "outlet_name" => $this->outlet ? $this->outlet->name : '',
            "roles" => $this->roles->map(function ($items){
                return $items->id;
            }),
            "roles_data"    => $this->roles ? $this->roles : [],             
        ];
    }
}
