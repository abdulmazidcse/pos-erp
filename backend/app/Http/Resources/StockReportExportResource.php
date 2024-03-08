<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockReportExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // item.product.purchase_unit ? item.product.purchase_unit.unit_code.toUpperCase() : ''
        return [
            'SL'    => $this->id,
            'item'    => $this->expires_date ? $this->product->product_name .' ('. $this->product->product_code .' || '. $this->expires_date.')' : $this->product->product_name .' ('. $this->product->product_code .')' ,
            'category'    => isset($this->product->sub_category) ? $this->product->sub_category->name : 'Default',
            'mrp_price'    => $this->product->mrp_price, 
            'cost_price'    => $this->product->cost_price, 
            'in_stock_quantity'    => $this->in_stock_quantity,
            'out_stock_quantity'    => $this->out_stock_quantity,
            'stock_quantity'    => $this->stock_quantity,
            'unit_code'    =>  $this->product->purchase_unit ? $this->product->purchase_unit->unit_code : '',
            'stock_weight_mrp_price' => ($this->stock_weight > 0) ? ($this->stock_weight * $this->product->mrp_price) : ($this->stock_quantity * $this->product->mrp_price),
            'stock_weight_cost_price' =>  ($this->stock_weight > 0) ? ($this->stock_weight * $this->product->cost_price) : ($this->stock_quantity * $this->product->cost_price),
        ];
    }
}
