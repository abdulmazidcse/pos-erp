<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'id'    => $this->id,
            'type_id' => $this->type_id,
            'type_name' => '',
            'name'  => $this->name,
            'address'   => $this->address,
            'district_id'   => $this->district_id,
            'district_name' => $this->districts ? $this->districts->name : "",
            'area_id'   => $this->area_id,
            'area_name' => $this->areas ? $this->areas->name : "",
            'postal_code'   => $this->postal_code,
            'contact_person_name' => $this->contact_person_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_image' => asset('/uploads/supplier/'.$this->logo_image),
            'supplier_payable_account'  => $this->payable_accounts ? $this->payable_accounts->ledger_name : $this->name,
            'supplier_discount_account'  => $this->discount_accounts ? $this->discount_accounts->ledger_name : $this->name,
            'supplier_advance_account'  => $this->advance_accounts ? $this->advance_accounts->ledger_name : $this->name,
            'outlet_receive'    => $this->outlet_receive,
            'payment_terms_conditions'  => $this->payment_terms_conditions,
            'payment_matured_days'  => $this->payment_matured_days,
            'commission_percent'  => $this->commission_percent,
            'supply_schedule'  => $this->supply_schedule,
            'damage_product'  => $this->damage_product,
            'slow_moving_product'  => $this->slow_moving_product,
            'short_dated_product'  => $this->short_dated_product,
            'expire_product'  => $this->expire_product,
            'bank_name'  => $this->bank_name,
            'branch_name'  => $this->branch_name,
            'routing_no'  => $this->routing_no,
            'account_name'  => $this->account_name,
            'account_number'  => $this->account_anumber,
            'status'  => $this->status,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'isPhoneAvailable'  => $this->phone != '' ? true : false,
        ];
    }
}
