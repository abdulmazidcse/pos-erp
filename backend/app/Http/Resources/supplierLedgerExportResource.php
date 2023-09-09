<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class supplierLedgerExportResource extends JsonResource
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
            'Sl'    => $this->id,
            'date'  => $this->transaction_date,
            'opening_balance' => $this->opening_balance,
            'purchases_amount' => $this->purchase_receive_amount,
            'payment' => $this->payment_amount,
            'return' => $this->return_amount,
            'discount' => $this->discount_amount,
            'closing_balance' => $this->closing_balance
        ];
    }
}
