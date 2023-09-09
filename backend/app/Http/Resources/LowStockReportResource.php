<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LowStockReportResource extends JsonResource
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
            'SL'=>$this->id,
            'item'    => $this->expires_date ? $this->product->product_name .' ('. $this->product->product_code .' || '. $this->expires_date.')' : $this->product->product_name .' ('. $this->product->product_code .')' ,
            'category'    => $this->product->category->name, 
            'Low_Stock_Qty' => $this->product->alert_quantity,
            'Stock_Qty' => $this->stock_quantity,
        ];
    }
}
