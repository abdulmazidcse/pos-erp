<?php

namespace App\Http\Controllers;

use App\Models\AccountClass;
use App\Models\AccountGroup;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\EntryType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReturn;
use App\Models\StockTransfer;
use App\Models\StoreRequisition;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message
        ], 200);
    }


    function uploadFile($file, $upload_path='', $prefix='', $height='', $width=''){
        $file_name = $prefix.time() . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('uploads/'.$upload_path), $file_name);
        return $file_name;
    }


    function removeFile($file, $upload_path=''){
        if (!empty($file)) {
            $old_file_path = str_replace('\\', '/', public_path()) . '/uploads/'.$upload_path.'/'. $file;
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }
    }


    // Purchase Order Reference No
    function returnPurchaseOrderNo($order_type=''){

        $currentDate = date("Ymd");
        $currentTime = date("his");
        if($order_type == 'RPO') {
            $lastPurchaseOrder = PurchaseOrder::whereNotNull('store_requisition_id')->orderBy('id', 'desc')->first();
            if(!empty($lastPurchaseOrder)){
                $get_serial = explode("-", $lastPurchaseOrder->reference_no);
                $current_serials = $get_serial[1] +1;
                $order_reference_no = 'RPO'.$currentDate.$currentTime.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $order_reference_no = 'RPO'.$currentDate.$currentTime.'-00001';
            }
        }else{
            $lastPurchaseOrder = PurchaseOrder::whereNull('store_requisition_id')->orderBy('id', 'desc')->first();
            if(!empty($lastPurchaseOrder)){
                $get_serial = explode("-", $lastPurchaseOrder->reference_no);
                $current_serials = $get_serial[1] +1;
                $order_reference_no = 'PO'.$currentDate.$currentTime.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $order_reference_no = 'PO'.$currentDate.$currentTime.'-00001';
            }
        }

        return $order_reference_no;
    }


    // Requisition No
    function returnStoreRequisitionNo($reference_id){
        $lastRequisition = StoreRequisition::where('outlet_id', $reference_id)->orderBy('id', 'desc')->first();

        $currentDate = date("Ymd");
        if(!empty($lastRequisition)){
            $get_serial = explode("-", $lastRequisition->requisition_no);
            $current_serials = $get_serial[1] +1;
            $requisition_no = 'SR'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
        }
        else{
            $requisition_no = 'SR'.$reference_id.$currentDate.'-00001';
        }
        return $requisition_no;
    }


    // Purchase Receive Reference No
    function returnPurchaseReceiveReferenceNo($receive_type="", $reference_id){

        $currentDate = date("Ymd");
        if($receive_type == "WR") {
            $lastPurchaseReceive = PurchaseReceive::where('warehouse_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReceive)){
                $get_serial = explode("-", $lastPurchaseReceive->reference_no);
                $current_serials = $get_serial[1] +1;
                $pr_reference_no = 'PRW'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'PRW'.$reference_id.$currentDate.'-00001';
            }
        }elseif($receive_type == "SR"){
            $lastPurchaseReceive = PurchaseReceive::where('outlet_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReceive)){
                $get_serial = explode("-", $lastPurchaseReceive->reference_no);
                $current_serials = $get_serial[1] + 1;
                $pr_reference_no = 'PRS'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'PRS'.$reference_id.$currentDate.'-00001';
            }
        }else{
            $lastPurchaseReceive = PurchaseReceive::where('outlet_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReceive)){
                $get_serial = explode("-", $lastPurchaseReceive->reference_no);
                $current_serials = $get_serial[1] +1;
                $pr_reference_no = 'PRSD'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'PRSD'.$reference_id.$currentDate.'-00001';
            }
        }

        return $pr_reference_no;
    }


    public function returnStockTransferReferenceNo()
    {
        $current_date = date("ymd");
        $current_time = date("his");

        $last_transfer_data = StockTransfer::orderBy('id', 'DESC')->first();
        if(!empty($last_transfer_data)) {
            $get_serial = explode("-", $last_transfer_data->reference_no);
            $current_serials = $get_serial[1] +1;
            $tr_reference_no = 'TR'.$current_date.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
        }else{
            $tr_reference_no = 'TR'.$current_date.'-'.str_pad(1, 5, '0', STR_PAD_LEFT);
        }

        return $tr_reference_no;
    }


    public function returnPurchaseReturnReferenceNo($reference_id, $return_type=""){
        $currentDate = date("Ymd");
        if($return_type == "WR") {
            $lastPurchaseReturn = PurchaseReturn::where('warehouse_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReturn)){
                $get_serial = explode("-", $lastPurchaseReturn->reference_no);
                $current_serials = $get_serial[1] +1;
                $pr_reference_no = 'WPR'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'WPR'.$reference_id.$currentDate.'-00001';
            }
        }elseif($return_type == "SR"){
            $lastPurchaseReturn = PurchaseReturn::where('outlet_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReturn)){
                $get_serial = explode("-", $lastPurchaseReturn->reference_no);
                $current_serials = $get_serial[1] + 1;
                $pr_reference_no = 'SPR'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'SPR'.$reference_id.$currentDate.'-00001';
            }
        }else{
            $lastPurchaseReturn = PurchaseReturn::where('outlet_id', $reference_id)->orderBy('id', 'DESC')->first();
            if(!empty($lastPurchaseReturn)){
                $get_serial = explode("-", $lastPurchaseReturn->reference_no);
                $current_serials = $get_serial[1] +1;
                $pr_reference_no = 'DSPR'.$reference_id.$currentDate.'-'.str_pad($current_serials, 5, '0', STR_PAD_LEFT);
            }
            else{
                $pr_reference_no = 'DSPR'.$reference_id.$currentDate.'-00001';
            }
        }

        return $pr_reference_no;
    }

    // Get Custom Group Code
    public function returnAccountClassCode($prefix='') {

        if($prefix == '' || $prefix == 0) {
            $group_code = AccountClass::max('code')+1;
        }else{
            $prefix_data = $prefix;
            $prefix_length = strlen($prefix_data);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $group_code = uniqueCodeWithPrefix($length, $prefix_data, 'account_classes', 'code');

        }

        return $group_code;
    }


    // Get Account Type Code
    public function returnAccountTypeCode($reference_id, $type='')
    {
        if($type == "group") {
            $group_data = AccountClass::where('id', $reference_id)->first();
            $prefix = $group_data->code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $account_code = uniqueCodeWithPrefix($length, $prefix, 'account_types', 'type_code');

        }elseif ($type == "type") {
            $type_data = AccountType::where('id', $reference_id)->first();
            $prefix = $type_data->type_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                case 6:
                    $length = $prefix_length + 2;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $account_code = uniqueCodeWithPrefix($length, $prefix, 'account_types', 'type_code');
        }elseif ($type == "detail_type") {
            $type_data = AccountType::where('id', $reference_id)->first();
            $prefix = $type_data->type_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                case 6:
                    $length = $prefix_length + 2;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $account_code = uniqueCodeWithPrefix($length, $prefix, 'account_types', 'type_code');
        }else{
            $account_code = AccountType::max('type_code')+1;
        }

        return $account_code;
    }


    public function returnAccountLedgerCode($group_id='', $parent_id='')
    {
        $ledger_code = '';
        $p_id = (int) $parent_id;
        if($p_id != '' || $p_id != 0) {
            $ledger_data = AccountLedger::where('id', $parent_id)->first();
            $prefix = $ledger_data->ledger_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                case 6:
                    $length = $prefix_length + 4;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $ledger_code = uniqueCodeWithPrefix($length, $prefix, 'account_ledgers', 'ledger_code');
        }

        if($group_id != '' || $group_id != 0) {
            $parent_group = AccountGroup::where('id', $group_id)->first();
            $prefix = $parent_group->group_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 1;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                case 6:
                    $length = $prefix_length + 4;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $ledger_code = uniqueCodeWithPrefix($length, $prefix, 'account_ledgers', 'ledger_code');
        }

        return $ledger_code;
    }

    // for Account Code
    public function returnAccountCode($reference_id='', $type) {

        if($type == "dtype") {
            $account_type   = AccountType::where('id', $reference_id)->first();
            $prefix = $account_type->type_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 4;
                    break;
                case 2:
                    $length = $prefix_length + 4;
                    break;
                case 4:
                    $length = $prefix_length + 4;
                    break;
                case 6:
                    $length = $prefix_length + 4;
                    break;
                case 8:
                    $length = $prefix_length + 4;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $account_code = uniqueCodeWithPrefix($length, $prefix, 'account_ledgers', 'ledger_code');

        }elseif($type == "paccount") {
            $parent_account   = AccountLedger::where('id', $reference_id)->first();
            $prefix = $parent_account->ledger_code;
            $prefix_length = strlen($prefix);

            switch ($prefix_length) {
                case 1:
                    $length = $prefix_length + 2;
                    break;
                case 2:
                    $length = $prefix_length + 2;
                    break;
                case 4:
                    $length = $prefix_length + 2;
                    break;
                case 6:
                    $length = $prefix_length + 2;
                    break;
                case 8:
                    $length = $prefix_length + 2;
                    break;
                case 10:
                    $length = $prefix_length + 2;
                    break;
                case 12:
                    $length = $prefix_length + 2;
                    break;
                default:
                    $length = 1;
                    break;
            }
            $account_code = uniqueCodeWithPrefix($length, $prefix, 'account_ledgers', 'ledger_code');
        }else{
            $account_code = AccountLedger::max('ledger_code')+1;
        }

        return $account_code;
    }


    public function returnVoucherCode($type)
    {
        if($type != "") {
            $entry_data = EntryType::where('label', $type)->first();
            $prefix = $entry_data->prefix . '-' . date("Ymd") . "-";

        $length = strlen($prefix) + 5;
        $voucher_code = uniqueGeneratedCodeWithPrefix($length, $prefix, 'account_vouchers', 'vcode');
        }else{
            //$prefix = 'OV-' . date("Ymd") . "-";
            $voucher_code = "";
        }
        return $voucher_code;
    }

    public function returnAutoVoucherCode($action) {
        if($action == "grn") {
            $prefix = 'AVP-' .date("Ymd") . '-';
        }else{
            $prefix = 'AV-' .date("Ymd") . '-';
        }
        $length = strlen($prefix) + 5;
        $voucher_code = uniqueGeneratedCodeWithPrefix($length, $prefix, 'account_vouchers', 'vcode');

        return $voucher_code;

    }



}
