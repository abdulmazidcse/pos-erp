<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreRequisitionResource extends JsonResource
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
            'requisition_no'   => $this->requisition_no,
            'requisition_date'   => $this->requisition_date,
            'outlet_id'   => ($this->outlet_id != 0) ? $this->outlet_id : '',
            'outlet_name'   => $this->outlets->name ?? '',
            'total_quantity'   => $this->total_quantity,
            'total_value'   => $this->total_value,
            'total_amount'   => $this->total_amount,
            'approve_status'   => $this->approve_status,
            'approve_status_name'   => $this->approveStatusName($this->approve_status),
            'order_status'   => $this->order_status,
            'order_status_name'   => $this->orderStatusName($this->order_status),
            'remarks'   => $this->remarks ?? 'N/A',
            'status'   => $this->status,
            'warehouse_id'   => ($this->warehouse_id != 0) ? $this->warehouse_id : '',
            'user_id'   => $this->user_id,
            'total_approve_quantity'   => 1234,
            'total_approve_amount'   => '',
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

    public function orderStatusName($order_status)
    {
        switch ($order_status){
            case 0:
                $order_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $order_status_name = '<span class="badge bg-info">Partial</span>';
                break;
            case 2:
                $order_status_name = '<span class="badge bg-success">Complete</span>';
                break;
            default:
                $order_status_name = '<span class="badge bg-default">N/A</span>';
                break;

        }

        return $order_status_name;
    }
}
