<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LedgerBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->reference_id != '') {
            $ledger_code    = $this->ledger_code;
            $ledger_id = $this->reference_id;
            $recursion_type="OL";
        }else{
            $ledger_code    = $this->ledger_code;
            $ledger_id = $this->ledger_code;
            $recursion_type="SL";
        }
        return [
            'id'            => $this->id,
            'voucher_id'    => $this->voucher_id,
            'voucher_code'    => $this->account_vouchers->vcode,
            'ledger_id'    => $this->ledger_id,
            'ledger_code'    => $this->ledger_code,
            'ledger_name'   => $this->account_ledgers->ledger_name ?? '',
            'vaccount_type'    => $this->vaccount_type,
            'reference_id'    => $this->reference_id,
            'debit'    => $this->debit,
            'credit'    => $this->credit,
            'vdate' => $this->account_vouchers->vdate,
            'global_note'   => $this->account_vouchers->global_note,
            'recursion' => $this->recursion($this->voucher_id, $ledger_id, $this->vaccount_type, $recursion_type, $ledger_code)
        ];
    }
}
