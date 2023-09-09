<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReportPurchaseResource extends JsonResource
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
            'purchase_date' => $this->purchase_receive->purchase_date,
            'supplier'     => $this->suppliers ? $this->suppliers->name : '',
            'product'  => $this->products->product_name, 
            'purchase_price'  => $this->receive_purchase_price, 
            'qty'           => ($this->receive_quantity - $this->receive_free_quantity), 
            'weight'        => ($this->receive_weight - $this->receive_free_quantity), 
            'amount'        => ($this->receive_product_unit_id ==5 ) ? (($this->receive_weight - $this->receive_free_quantity) * $this->receive_purchase_price ) : (($this->receive_quantity - $this->receive_free_quantity) * $this->receive_purchase_price)
        ];
    }
}
     
      
 
