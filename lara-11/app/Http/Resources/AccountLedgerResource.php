<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountLedgerResource extends JsonResource
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
            'ledger_name'   => $this->ledger_name,
            'ledger_code'   => $this->ledger_code,
            'parent_id'   => $this->parent_id,
            'type_id'   => $this->type_id,
            'detail_type_id'   => $this->detail_type_id,
            'ledger_type'   => $this->ledger_type,
            'opening_balance'   => $this->opening_balance,
            'balance_date'  => $this->balance_date,
            'is_control_transaction'   => $this->is_control_transaction,
            'is_master_head'   => $this->is_master_head,
            'status'   => $this->status,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'deleted_at'   => $this->deleted_at,
            'detail_type_name' => $this->account_types ? $this->account_types->type_name : 'N/A',
            'type_name' => $this->account_types->type_parents ? $this->account_types->type_parents->type_name : 'N/A',
            'class_id'  => $this->account_types->type_parents ? $this->account_types->type_parents->class_id : '',

        ];
    }
}
