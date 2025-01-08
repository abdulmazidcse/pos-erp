<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesReportResource extends JsonResource
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
            'reference_no' => $this->invoice_number,
            'customer_name' => $this->customer_name,
            'total_amount' => $this->grand_total,
            'order_discount' => $this->order_discount,
            'paid_amount' => $this->paid_amount,
            'due' => ($this->grand_total - $this->paid_amount)
        ];
    }
}
