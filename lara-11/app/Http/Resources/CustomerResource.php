<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'customer_code'    => $this->customer_code,
            'emp_code'  => $this->emp_code,
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address'   => $this->address,
            'dob'   => $this->dob,
            'total_points'   => $this->totalPoints->sum('points'),
            'total_redeem'   => $this->totalRedeem->sum('points'),
            'available_point'   => $this->totalPoints->sum('points') - $this->totalRedeem->sum('points'),
            'company_id'   =>   $this->company_id,
            'company_name'  => $this->company->name ?? "N/A",
            'customer_group_id' => $this->customer_group_id,
            'customer_group_name'   => $this->customer_group->title ?? "N/A",
            'customer_group_discount'   => $this->customer_group->value ?? "N/A",
            'district_id'   => $this->district_id,
            'district_name' => $this->districts->name ?? "N/A",
            'area_id'   => $this->area_id,
            'area_name' => $this->areas->name ?? "N/A",
            'postal_code'   => $this->postal_code,
            'discount_percent'  => $this->discount_percent,
            'wholesale_customer'    => $this->wholesale_customer,
            'sale_without_vat'    => $this->sale_without_vat,
            'credit_customer'    => $this->credit_customer,
            'store_customer'    => $this->store_customer,
            'receivable_ledger_id'  => $this->receivable_ledger_id,
            'customer_receivable_account'   => $this->receivable_accounts ? $this->receivable_accounts->ledger_name : '',
            'status'    => $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at

        ];
    }
}
