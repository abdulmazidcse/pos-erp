<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $area_name  = $this->areas ? $this->areas->name : 'Dhaka';
        $district_name  = $this->districts ? $this->districts->name : 'Dhaka';
        return [
            'id'            => $this->id,
            'company_id'    => $this->company_id,
            'name'          => $this->name,
            'contact_person_name' => $this->contact_person_name,
            'outlet_number' => $this->outlet_number,
            'district_id'      => $this->district_id,            
            'district_name' => $district_name,
            'area_id'       => $this->area_id,
            'area_name'     => $area_name ,
            'police_station'=> $this->police_station,
            'road_no'       => $this->road_no,
            'plot_no'       => $this->plot_no,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude, 
            'address'       => $this->plot_no.', '. $this->road_no.', '. $area_name.', '. $this->police_station.', '. $district_name,
            'addrs'         => $this->address,
            'status'        => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'company'       => $this->company,
        ];
    }
}
