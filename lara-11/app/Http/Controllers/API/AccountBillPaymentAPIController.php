<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\PurchaseReceive;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountBillPaymentAPIController extends AppBaseController
{
    public function billPayment(Request $request)
    {
        $fiscal_year = FiscalYear::where('status', 1)->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;
        $this->validate($request, [
            'supplier_id' => 'required',
            'ledger_id' => 'required',
            'invoice_items' => 'required',
            'date'  => 'date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date
        ]);
        // Supplier Ledger
        // purchase receive
        // account voucher,
        // account voucher transaction,

        DB::beginTransaction();
        try {
            $supplier_id    = $request->get('supplier_id');
            $invoice_items = (object) json_decode($request->get('invoice_items'));
            $supplier_ledger_ids = array();
            $total_payment_amount = 0;
            foreach ($invoice_items as $invoice_item) {

                $purchase_receive = PurchaseReceive::where('id', $invoice_item->receive_id)->first();
                if($invoice_item->payable_amount == $invoice_item->paid_amount)
                {
                    $payment_status = "paid";
                }else{
                    $payment_status = "partial";
                }
                $payment_amount = $invoice_item->paid_amount;
                $invoice_update = [
                    'paid_amount'   => $payment_amount,
                    'payment_status'    => $payment_status
                ];

                // Purchase Receive Update
                $purchase_receive_update = $purchase_receive->update($invoice_update);

                $supplier = Supplier::where('id', $supplier_id)->first();
                $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'desc')->first();
                if(empty($supplier_ledger)) {
                    $supplier_opening_balance = 0;
                }else{
                    $supplier_opening_balance = $supplier_ledger->closing_balance;
                }

                $supplier_closing_balance = $supplier_opening_balance - $payment_amount;
                $supplier_ledger_inputs = [
                    'supplier_id'   => $request->get('supplier_id'),
                    'transaction_type'  => 'VBP',
                    'opening_balance'   => $supplier_opening_balance,
                    'payment_amount'   => $payment_amount,
                    'closing_balance'   => $supplier_closing_balance,
                    'transaction_date'  => date("Y-m-d"),
                ];

                $suppler_ledger_save    = SupplierLedger::create($supplier_ledger_inputs);
                $supplier_ledger_ids[]  = $suppler_ledger_save->id;

                $total_payment_amount += $payment_amount;
            }

            $entry_type = EntryType::where('label', 'payment')->first();
            $voucher_code   = $this->returnVoucherCode('payment');
            $account_voucer_inputs  = [
                'vcode' => $voucher_code,
                'vtype_id'  => $entry_type->id,
                'vtype_value'   => $entry_type->label,
                'fiscal_year_id'    => $fiscal_year->id,
                'vdate' => $request->get('date'),
                'global_note'   => 'Supplier payment for purchase product',
                'modified_item' => 0,
            ];

            // For Central Ledger
            $specific_ledger = ($supplier->payable_accounts) ? $supplier : "";
            $ledger_data    = getLedgerAccounts('grn_payment', $specific_ledger);
            $transactions = [];
            if(count($ledger_data) > 0) {
                $t = 0;
                foreach ($ledger_data as $key => $v) {
                    if($v == 'D') {
                        $account_ledger = AccountLedger::where('ledger_code', $key)->first();
                        $debit_amount = $total_payment_amount;
                        $credit_amount = 0;
                        $vaccount_type = 'dr';
                    }else{
                        $account_ledger = AccountLedger::where('id', $request->get('ledger_id'))->first();
                        $credit_amount = $total_payment_amount;
                        $debit_amount = 0;
                        $vaccount_type = 'cr';
                    }

                    if($t == 0) {
                        $reference_id = NULL;
                    }else{
                        $reference_id = array_keys($ledger_data)[0];
                    }

                    $transactions[] = new AccountVoucherTransaction([
                        'cost_center_id' => 2, // Based on selected cost center
                        'ledger_id' => $account_ledger->id,
                        'ledger_code'   => $account_ledger->ledger_code,
                        'vaccount_type' => $vaccount_type,
                        'debit' => $debit_amount,
                        'credit'    => $credit_amount,
                        'reference_id'  => $reference_id,
                        'transaction_sl'    => 1,
                        'created_at'    => date("Y-m-d H:i:s"),
                        'updated_at'    => date("Y-m-d H:i:s"),
                    ]);

                    $t++;
                }

                $voucher_save   = AccountVoucher::create($account_voucer_inputs);
                $account_transaction = $voucher_save->account_voucher_transactions()->saveMany($transactions);
                if(count($supplier_ledger_ids) > 0) {
                    $supplier_ledger_update = SupplierLedger::whereIn('id', $supplier_ledger_ids)->update(['voucher_id' => $voucher_save->id]);
                }

            }
            DB::commit();
            return $this->sendSuccess('Bill Payment Successfully done!');
        }catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }

    }

}
