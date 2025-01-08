<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'store_requisition_id'   => $this->store_requisition_id ?? 0,
            'store_requisition_no' => $this->store_requisition ? $this->store_requisition->requisition_no : 'N/A',
            'reference_no'   => $this->reference_no,
            'supplier_id'   => $this->supplier_id,
            'supplier_name'   => $this->suppliers ? $this->suppliers->name : '',
            'supplier_payment_type'   => $this->supplier_payment_type,
            'number_of_po'   => $this->number_of_po,
            'supply_schedule'   => $this->supply_schedule,
            'order_date'   => $this->order_date,
            'delivery_date'   => $this->delivery_date,
            'delivery_to_outlet'   => $this->delivery_to_outlet,
            'outlet_id'   => ($this->delivery_to_outlet != 0) ? $this->delivery_to_outlet : '',
            'start_date'   => $this->start_date,
            'end_date'   => $this->end_date,
            'total_qty'   => $this->total_qty,
            'total_value'   => $this->total_value,
            'commission_value'   => $this->commission_value,
            'total_vat'   => $this->total_vat,
            'total_free_amount'   => $this->total_free_amount,
            'total_amount'   => $this->total_amount,
            'approve_status'   => $this->approve_status,
            'approve_status_name'   => $this->approveStatusName($this->approve_status),
            'send_status'       => $this->send_status,
            'send_status_name'   => $this->sendStatusName($this->send_status),
            'receive_status'   => $this->receive_status,
            'receive_status_name'   => $this->receiveStatusName($this->receive_status),
            'remarks'   => $this->remarks,
            'status'   => $this->status,
            'warehouse_id'   => ($this->warehouse_id != 0) ? $this->warehouse_id : '',
            'user_id'   => $this->user_id,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }


    public function approveStatusName($approve_status)
    {
        switch ($approve_status){
            case 0:
                $approve_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $approve_status_name = '<span class="badge bg-success">Approved</span>';
                break;
            case 2:
                $approve_status_name = '<span class="badge bg-danger">Reject</span>';
                break;
            default:
                $approve_status_name = '<span class="badge bg-default">N/A</span>';
                break;

        }

        return $approve_status_name;
    }


    public function sendStatusName($send_status)
    {
        switch ($send_status){
            case 0:
                $send_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $send_status_name = '<span class="badge bg-success">Done</span>';
                break;
            default:
                $send_status_name = '<span class="badge bg-default">N/A</span>';
                break;

        }

        return $send_status_name;
    }


    public function receiveStatusName($receive_status)
    {
        switch ($receive_status){
            case 0:
                $receive_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $receive_status_name = '<span class="badge bg-success">Received</span>';
                break;
            case 2:
                $receive_status_name = '<span class="badge bg-danger">Partial</span>';
                break;
            default:
                $receive_status_name = '<span class="badge bg-default">N/A</span>';
                break;

        }

        return $receive_status_name;
    }
}
