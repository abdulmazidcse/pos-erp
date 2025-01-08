<?php

namespace App\Http\Resources;

use App\Models\StockProduct;
use Illuminate\Http\Resources\Json\JsonResource;

class InventorySummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $i = 0;
        return [
            'SL'    => $i + 1,
            'name'  => $this->name,
            'item_count'    => $this->item_count,
            'stock_quantity'    => $this->stock_quantity,
            'stock_purchase_amount'    => $this->stock_purchase_amount,
            'stock_sale_amount'    => $this->stock_sale_amount,
        ];
    }
}
