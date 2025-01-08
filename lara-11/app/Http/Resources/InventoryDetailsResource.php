<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryDetailsResource extends JsonResource
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
            'SL'    => $this->id,
            // 'category'    => $this->product->sub_category ? $this->product->sub_category->name : 'Default',
            'category'    => isset($this->product->sub_category) ? $this->product->sub_category->name : 'Default',
            'item'    => $this->expires_date ? $this->product->product_name .' ('. $this->product->product_code .' || '. $this->expires_date.')' : $this->product->product_name .' ('. $this->product->product_code .')' ,
            'unit_code'    =>  $this->product->purchase_unit ? $this->product->purchase_unit->unit_code : '',
            'cost_price'    => $this->product->cost_price,
            'mrp_price'    => $this->product->mrp_price,
            'stock_quantity'    => $this->stock_quantity,
            'stock_purchase_amount' =>  ($this->stock_quantity * $this->product->cost_price),
            'stock_sale_amount' =>  ($this->stock_quantity * $this->product->mrp_price),
        ];
    }
}
