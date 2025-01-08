<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportPurchaseResource extends JsonResource
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
            'Sl'            => $this->id,
            'purchase_date' => $this->purchase_date,
            'reference_no'  => $this->reference_no,
            'suppliers'     => $this->suppliers ? $this->suppliers->name : '',
            'net_amount'    => $this->net_amount,
        ];
    }
}
