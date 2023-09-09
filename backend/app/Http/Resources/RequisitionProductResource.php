<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequisitionProductResource extends JsonResource
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
            'id'               => $this->id,
            'product_type'     => $this->product_type,
            'product_name'     => $this->product_name,
            'product_native_name' => $this->product_native_name,
            'product_code'     => $this->product_code,
            'category_id'      => $this->category_id,
            'sub_category_id'      => $this->sub_category_id,
            'barcode_symbology'=> $this->barcode_symbology,
            'min_order_qty'    => $this->min_order_qty,
            'cost_price'       => $this->cost_price,
            'depo_price'       => $this->depo_price,
            'mrp_price'        => $this->mrp_price,
            'tax_method'       => $this->tax_method,
            'product_tax'      => $this->product_tax,
            'quantity'         => $this->quantity,
            'user_define_barcode'=> $this->product_barcodes->map(function($item) { return $item->code; }),
            'status'           => $this->status,
        ];
    }
}
