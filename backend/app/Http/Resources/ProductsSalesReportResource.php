<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsSalesReportResource extends JsonResource
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
            'Sl' => $this->id,
            'data' => $this->created_at,
            'customer_name' => $this->sales ? $this->sales->customer_name : '',
            'product' => $this->products->product_name,
            'price' => $this->mrp_price,
            'qty' => $this->quantity .'||'. $this->weight,
            'amount' => (($this->quantity * $this->mrp_price) .' || '. ($this->weight * $this->mrp_price)) , 
        ];
    }
} 
