<?php

namespace App\Imports;

use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\EntryType;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockProduct;
use App\Models\StockProductsAdjustment;
use App\Models\StockProductsLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockProductsAdjustmentImport implements
    ToCollection,
    WithHeadingRow
{
    use Importable;
    /**
    * @param Collection $collection
    */

    public $adjustment_data, $fiscal_year_id;
    public function __construct($fiscal_year_data)
    {
        $this->fiscal_year_id = $fiscal_year_data->id;
    }

    public function collection(Collection $rows)
    {

        $this->adjustment_data = $rows;
        $total_in_stock_qty = 0;
        $total_in_stock_amount = 0;

        $total_out_stock_qty = 0;
        $total_out_stock_amount = 0;

        foreach($rows as $row) {

            $product = Product::where('product_name', $row['product_name'])->orWhere('product_code', $row['product_code'])->first();
            $outlet = Outlet::where('name', $row['outlet_name'])->first();

            if(!empty($product) && !empty($outlet)) {

                $new_in_stock_quantity = $row['in_stock_quantity'] ?? 0;
                $new_out_stock_quantity = $row['out_stock_quantity'] ?? 0;

                if($new_in_stock_quantity > 0) {
                    $in_stock_amount    = ($new_in_stock_quantity * $product->cost_price);
                    $total_in_stock_amount += $in_stock_amount;
                    $total_in_stock_qty += $new_in_stock_quantity;
                }

                if($new_out_stock_quantity > 0) {
                    $out_stock_amount    = ($new_out_stock_quantity * $product->cost_price);
                    $total_out_stock_amount += $out_stock_amount;
                    $total_out_stock_qty    += $new_out_stock_quantity;
                }

                // Stock Adjustment With Expires Date
                if(!empty($row['expires_date'])) {
                    $expires_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expires_date']))->format("Y-m-d");
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereDate('expires_date', $expires_date)->first();


                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    $log_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'expires_date'  => $expires_date,
                        'stock_type'    => 'ADJ',
                        'note'          => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    $adjustment_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'expires_date'  => $expires_date,
                        'note'  => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    if(!empty($stock_product_data)) {

                        $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
                        $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
                        $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);

                        $update_inputs = [
                            'in_stock_quantity' => $update_in_stock_quantity,
                            'stock_quantity' => $update_stock_quantity,
                            'out_stock_quantity' => $update_out_stock_quantity,
                        ];

                        $stock_update = $stock_product_data->update($update_inputs);

                        $log_create = StockProductsLog::create($log_insert_input);
                        $adjustment_create = StockProductsAdjustment::create($adjustment_insert_input);
                    }

                }
                // Stock Adjustment Without Expires Date
                else{
                    $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereNull('expires_date')->first();

                    $old_stock = $stock_product_data->stock_quantity ?? 0;
                    $log_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'stock_type'    => 'ADJ',
                        'note'          => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    $adjustment_insert_input = [
                        'product_id'    => $product->id,
                        'outlet_id'     => $outlet->id,
                        'in_stock_quantity' => $new_in_stock_quantity,
                        'out_stock_quantity'    => $new_out_stock_quantity,
                        'note'  => $row['note'],
                        'user_id'   => auth()->user()->id ?? 1,
                    ];

                    if(!empty($stock_product_data)) {

                        $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
                        $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
                        $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);

                        $update_inputs = [
                            'in_stock_quantity' => $update_in_stock_quantity,
                            'stock_quantity' => $update_stock_quantity,
                            'out_stock_quantity' => $update_out_stock_quantity,
                        ];

                        $stock_update = $stock_product_data->update($update_inputs);

                        $log_create = StockProductsLog::create($log_insert_input);
                        $adjustment_create = StockProductsAdjustment::create($adjustment_insert_input);
                    }

                }

            }

        }

        // Adjustment Transaction
        $data = [
            'fiscal_year_id'    => $this->fiscal_year_id,
            'total_in_stock_qty' => $total_in_stock_qty,
            'total_in_stock_amount' => $total_in_stock_amount,
            'total_out_stock_qty' => $total_out_stock_qty,
            'total_out_stock_amount' => $total_out_stock_amount,
        ];

        // For Stock Adjustment
//        $this->inventoryAdjustmentTransaction($data);

    }


    protected function inventoryAdjustmentTransaction($data=array())
    {
        $entry_type = EntryType::where('label', 'journal')->first();
        $inventory_ledger = getLedgerAccountData('ledger_code', '120101'); // dr/cr assets
        $inventory_adjust_ledger = getLedgerAccountData('ledger_code', '420107'); // cr/dr income

        // Increase Inventory
        if($data['total_in_stock_qty'] > 0 && $data['total_in_stock_amount'] > 0) {
            $voucher_code_increase   = $this->returnVoucherCode('journal');
            $account_voucher_inputs_increase  = [
                'vcode' => $voucher_code_increase,
                'vtype_id'  => $entry_type->id,
                'vtype_value'   => 'auto voucher',
                'fiscal_year_id'    => $data['fiscal_year_id'],
                'vdate' => date("Y-m-d"),
                'global_note'   => 'Inventory Adjustment',
                'modified_item' => 0,
            ];

            $transactions_increase = [
                // first Transaction
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'dr',
                    'ledger_id' => $inventory_ledger->id,
                    'ledger_code' => $inventory_ledger->ledger_code,
                    'debit' => $data['total_in_stock_amount'],
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
                    'credit' => $data['total_in_stock_amount'],
                    'reference_id' => $inventory_ledger->ledger_code,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),

            ];

            $voucher_save_increase = AccountVoucher::create($account_voucher_inputs_increase);
            $transactions_save = $voucher_save_increase->account_voucher_transactions()->saveMany($transactions_increase);

        }

        // Decrease Inventory
        if($data['total_out_stock_qty'] > 0 && $data['total_out_stock_amount'] > 0) {

            $voucher_code_decrease   = $this->returnVoucherCode('journal');
            $account_voucher_inputs_decrease  = [
                'vcode' => $voucher_code_decrease,
                'vtype_id'  => $entry_type->id,
                'vtype_value'   => 'auto voucher',
                'fiscal_year_id'    => $data['fiscal_year_id'],
                'vdate' => date("Y-m-d"),
                'global_note'   => 'Inventory Adjustment',
                'modified_item' => 0,
            ];

            $transactions_decrease = [
                // first Transaction
                new AccountVoucherTransaction([
                    'cost_center_id' => 2,
                    'vaccount_type' => 'dr',
                    'ledger_id' => $inventory_adjust_ledger->id,
                    'ledger_code' => $inventory_adjust_ledger->ledger_code,
                    'debit' => $data['total_out_stock_amount'],
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
                    'credit' => $data['total_out_stock_amount'],
                    'reference_id' => $inventory_adjust_ledger->ledger_code,
                    'transaction_sl' => 1,
                    'voucher_note' => null,
                    'balance' => 0,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                ]),

            ];

            $voucher_save_decrease = AccountVoucher::create($account_voucher_inputs_decrease);
            $transactions_save = $voucher_save_decrease->account_voucher_transactions()->saveMany($transactions_decrease);

        }


    }

    protected function returnVoucherCode($type, $sl=0)
    {
        if($type != "") {
            $entry_data = EntryType::where('label', $type)->first();
            $prefix = $entry_data->prefix . '-' . date("Ymd") . "-";

            $length = strlen($prefix) + 5;
            $voucher_code = uniqueGeneratedCodeWithPrefix($length, $prefix, 'account_vouchers', 'vcode', $sl);
        }else{
            //$prefix = 'OV-' . date("Ymd") . "-";
            $voucher_code = "";
        }
        return $voucher_code;
    }

//    public function collection(Collection $rows)
//    {
//
//        $this->return_data = $rows;
//        foreach($rows as $row) {
//
//            $product = Product::where('product_name', $row['product_name'])->orWhere('product_code', $row['product_code'])->first();
//            $outlet = Outlet::where('name', $row['outlet_name'])->first();
//
//            $new_in_stock_quantity = $row['in_stock_quantity'] ?? 0;
//            $new_out_stock_quantity = $row['out_stock_quantity'] ?? 0;
//
//            // Stock Adjustment With Expires Date
//            if(!empty($row['expires_date'])) {
//                $expires_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expires_date']))->format("Y-m-d");
//                $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereDate('expires_date', $expires_date)->first();
//
//
//                if(empty($stock_product_data)) {
//                    $insert_inputs = [
//                        'product_id'    => $product->id,
//                        'outlet_id'     => $outlet->id,
//                        'in_stock_quantity' => $new_in_stock_quantity,
//                        'stock_quantity' => $new_in_stock_quantity - $new_out_stock_quantity,
//                        'out_stock_quantity' => $new_out_stock_quantity,
//                        'expires_date'  => $expires_date,
//                    ];
//
//
//                    $stock_save = StockProduct::create($insert_inputs);
//                }else{
//                    $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
//                    $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
//                    $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);
//
//                    $update_inputs = [
//                        'in_stock_quantity' => $update_in_stock_quantity,
//                        'stock_quantity' => $update_stock_quantity,
//                        'out_stock_quantity' => $update_out_stock_quantity,
//                    ];
//
//                    $stock_update = $stock_product_data->update($update_inputs);
//                }
//
//                $old_stock = $stock_product_data->stock_quantity ?? 0;
//                $log_insert_input = [
//                    'product_id'    => $product->id,
//                    'outlet_id'     => $outlet->id,
//                    'in_stock_quantity' => $new_in_stock_quantity,
//                    'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
//                    'out_stock_quantity'    => $new_out_stock_quantity,
//                    'expires_date'  => $expires_date,
//                    'stock_type'    => 'ADJ',
//                    'note'          => $row['note'],
//                    'user_id'   => auth()->user()->id ?? 1,
//                ];
//
//                $adjustment_insert_input = [
//                    'product_id'    => $product->id,
//                    'outlet_id'     => $outlet->id,
//                    'in_stock_quantity' => $new_in_stock_quantity,
//                    'out_stock_quantity'    => $new_out_stock_quantity,
//                    'expires_date'  => $expires_date,
//                    'note'  => $row['note'],
//                    'user_id'   => auth()->user()->id ?? 1,
//                ];
//
//                $log_create = StockProductsLog::create($log_insert_input);
//                $adjustment_create = StockProductsAdjustment::create($adjustment_insert_input);
//
//            }
//            // Stock Adjustment Without Expires Date
//            else{
//                $stock_product_data = StockProduct::where('product_id', $product->id)->where('outlet_id', $outlet->id)->whereNull('expires_date')->first();
//                if(empty($stock_product_data)) {
//                    $insert_inputs = [
//                        'product_id'    => $product->id,
//                        'outlet_id'     => $outlet->id,
//                        'in_stock_quantity' => $new_in_stock_quantity,
//                        'stock_quantity' => $new_in_stock_quantity - $new_out_stock_quantity,
//                        'out_stock_quantity' => $new_out_stock_quantity,
//                    ];
//
//                    $stock_save = StockProduct::create($insert_inputs);
//                }else{
//                    $update_in_stock_quantity = ($stock_product_data->in_stock_quantity + $new_in_stock_quantity);
//                    $update_stock_quantity = (($stock_product_data->stock_quantity + $new_in_stock_quantity) - $new_out_stock_quantity);
//                    $update_out_stock_quantity = ($stock_product_data->out_stock_quantity + $new_out_stock_quantity);
//
//                    $update_inputs = [
//                        'in_stock_quantity' => $update_in_stock_quantity,
//                        'stock_quantity' => $update_stock_quantity,
//                        'out_stock_quantity' => $update_out_stock_quantity,
//                    ];
//
//                    $stock_update = $stock_product_data->update($update_inputs);
//                }
//
//                $old_stock = $stock_product_data->stock_quantity ?? 0;
//                $log_insert_input = [
//                    'product_id'    => $product->id,
//                    'outlet_id'     => $outlet->id,
//                    'in_stock_quantity' => $new_in_stock_quantity,
//                    'stock_quantity'    => ($old_stock + $new_in_stock_quantity),
//                    'out_stock_quantity'    => $new_out_stock_quantity,
//                    'stock_type'    => 'ADJ',
//                    'note'          => $row['note'],
//                    'user_id'   => auth()->user()->id ?? 1,
//                ];
//
//                $adjustment_insert_input = [
//                    'product_id'    => $product->id,
//                    'outlet_id'     => $outlet->id,
//                    'in_stock_quantity' => $new_in_stock_quantity,
//                    'out_stock_quantity'    => $new_out_stock_quantity,
//                    'note'  => $row['note'],
//                    'user_id'   => auth()->user()->id ?? 1,
//                ];
//
//                $log_create = StockProductsLog::create($log_insert_input);
//                $adjustment_create = StockProductsAdjustment::create($adjustment_insert_input);
//            }
//
//        }
//    }


}
