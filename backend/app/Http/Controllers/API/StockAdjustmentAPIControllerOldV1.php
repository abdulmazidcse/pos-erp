<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Imports\StockProductsAdjustmentImport;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\StockProductsAdjustment;
use App\Models\StockProductsLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockAdjustmentAPIController extends AppBaseController
{
    public function stockAdjustment(Request $request)
    {
        $stock_data = StockProduct::find($request->stock_id);
        if(empty($stock_data)) {
            return $this->sendError('Stock Data Not Found');
        }

        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }


        if((empty($request->get('in_stock_quantity')) && $request->get('in_stock_quantity') == 0) && (empty($request->get('out_stock_quantity')) && $request->get('out_stock_quantity') == 0)) {
            return $this->sendError('In Stock Or Out Stock Quantity must be getter than 0');
        }
        $new_in_stock_quantity = $request->get('in_stock_quantity') ?? 0;
        $new_out_stock_quantity = $request->get('out_stock_quantity') ?? 0;

        $unit_code = $request->get('unit_code');
        if($unit_code == "kg") {
            $update_inputs = [
                'in_stock_weight' => ($stock_data->in_stock_weight + $new_in_stock_quantity),
                'stock_weight'    => (($stock_data->stock_weight + $new_in_stock_quantity) - $new_out_stock_quantity),
                'out_stock_weight' => ($stock_data->out_stock_weight + $new_out_stock_quantity)
            ];

            $stock_log_inputs = [
                'product_id'    => $stock_data->product_id,
                'outlet_id'     => $stock_data->outlet_id,
                'in_stock_weight' => $new_in_stock_quantity,
                'stock_weight'    => ($stock_data->stock_weight + $new_in_stock_quantity),
                'out_stock_weight'    => $new_out_stock_quantity,
                'expires_date'  => $stock_data->expires_date,
                'stock_type'    => 'ADJ',
                'user_id'   => auth()->user()->id ?? 1,
            ];

            $adjustment_inputs = [
                'product_id'    => $stock_data->product_id,
                'outlet_id'     => $stock_data->outlet_id,
                'in_stock_weight' => $new_in_stock_quantity,
                'out_stock_weight'    => $new_out_stock_quantity,
                'expires_date'  => $stock_data->expires_date,
                'user_id'   => auth()->user()->id ?? 1,
            ];

        }else {
            $update_inputs = [
                'in_stock_quantity' => ($stock_data->in_stock_quantity + $new_in_stock_quantity),
                'stock_quantity'    => (($stock_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity),
                'out_stock_quantity' => ($stock_data->out_stock_quantity + $new_out_stock_quantity)
            ];

            $stock_log_inputs = [
                'product_id'    => $stock_data->product_id,
                'outlet_id'     => $stock_data->outlet_id,
                'in_stock_quantity' => $new_in_stock_quantity,
                'stock_quantity'    => ($stock_data->stock_quantity + $new_in_stock_quantity),
                'out_stock_quantity'    => $new_out_stock_quantity,
                'expires_date'  => $stock_data->expires_date,
                'stock_type'    => 'ADJ',
                'user_id'   => auth()->user()->id ?? 1,
            ];

            $adjustment_inputs = [
                'product_id'    => $stock_data->product_id,
                'outlet_id'     => $stock_data->outlet_id,
                'in_stock_quantity' => $new_in_stock_quantity,
                'out_stock_quantity'    => $new_out_stock_quantity,
                'expires_date'  => $stock_data->expires_date,
                'user_id'   => auth()->user()->id ?? 1,
            ];
        }


        $product_data   = Product::where('id', $stock_data->product_id)->first();
        $transaction_required_array     = [
            'product_cost_price'    => $product_data->cost_price,
            'increase_stock_qty'    => $new_in_stock_quantity,
            'decrease_stock_qty'    => $new_out_stock_quantity,
            'fiscal_year_id'        => $fiscal_year->id,
        ];



        DB::beginTransaction();
        try {
            $update_stock = $stock_data->update($update_inputs);
            $adjustment_inputs = StockProductsAdjustment::create($adjustment_inputs);
            $log_insert = StockProductsLog::create($stock_log_inputs);
            $transaction_save = $this->inventoryAdjustmentTransaction($transaction_required_array);

            DB::commit();
            return $this->sendSuccess('Stock Adjustment Successfully Done!');

        }catch(\Exception $e){
            DB::rollBack();
            return $this->sendError($e->getMessage(), $e->getCode());
        }


    }


    public function stockBulkAdjustment(Request $request)
    {
        $this->validate($request, [
            'excel_file'    => 'required',
        ]);

        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;

        if($start_date >= date("Y-m-d") && date("Y-m-d") <= $end_date) {
            return $this->sendError("Date must be range of Fiscal Year");
        }

        $file = $request->file('excel_file');

        $importObj  = new StockProductsAdjustmentImport($fiscal_year);
        $import = $importObj->import($file);

//        return response()->json($importObj->adjustment_data);

        if($import){
            return $this->sendSuccess('Bulk adjustment successfully done!');
        }else{
            return $this->sendError('Something went wrong, please try again!');
        }

    }


    public function inventoryAdjustmentTransaction($data=array())
    {

        $entry_type = EntryType::where('label', 'journal')->first();
        $voucher_code   = $this->returnVoucherCode('journal');
        $account_voucher_inputs  = [
            'vcode' => $voucher_code,
            'vtype_id'  => $entry_type->id,
            'vtype_value'   => 'auto voucher',
            'fiscal_year_id'    => $data['fiscal_year_id'],
            'vdate' => date("Y-m-d"),
            'global_note'   => 'Inventory Adjustment',
            'modified_item' => 0,
        ];

        // 1st Transaction
        $inventory_ledger = getLedgerAccountData('ledger_code', '120101'); // dr/cr assets
        $inventory_adjust_ledger = getLedgerAccountData('ledger_code', '420107'); // cr/dr income


        if($data['increase_stock_qty'] > 0) {
            $transaction_amount = $data['increase_stock_qty'] * $data['product_cost_price'];
            $transactions = [
                // first Transaction
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'dr',
                    'ledger_id' => $inventory_ledger->id,
                    'ledger_code' => $inventory_ledger->ledger_code,
                    'debit' => $transaction_amount,
                    'credit' => 0,
                    'reference_id' => null,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'cr',
                    'ledger_id' => $inventory_adjust_ledger->id,
                    'ledger_code' => $inventory_adjust_ledger->ledger_code,
                    'debit' => 0,
                    'credit' => $transaction_amount,
                    'reference_id' => $inventory_ledger->ledger_code,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),

            ];

        }else{
            $transaction_amount = $data['decrease_stock_qty'] * $data['product_cost_price'];
            $transactions = [
                // first Transaction
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'dr',
                    'ledger_id' => $inventory_adjust_ledger->id,
                    'ledger_code' => $inventory_adjust_ledger->ledger_code,
                    'debit' => $transaction_amount,
                    'credit' => 0,
                    'reference_id' => null,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'dr',
                    'ledger_id' => $inventory_ledger->id,
                    'ledger_code' => $inventory_ledger->ledger_code,
                    'debit' => 0,
                    'credit' => $transaction_amount,
                    'reference_id' => $inventory_adjust_ledger->ledger_code,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),

            ];
        }

//        $transaction_array = [];
//        foreach ($transactions as $transaction) {
//            if($transaction->debit > 0 || $transaction->credit > 0) {
//                $transaction_array[] = $transaction;
//            }
//        }
        $voucher_save = AccountVoucher::create($account_voucher_inputs);
        $transactions_save = $voucher_save->account_voucher_transactions()->saveMany($transactions);

        return $voucher_save;
    }

}
