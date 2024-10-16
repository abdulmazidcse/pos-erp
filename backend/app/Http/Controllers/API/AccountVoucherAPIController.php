<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountVoucherAPIRequest;
use App\Http\Requests\API\UpdateAccountVoucherAPIRequest;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\CustomerLedger;
use App\Models\EntryType;
use App\Models\FiscalYear;
use App\Models\SupplierLedger;
use App\Repositories\AccountVoucherRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class AccountVoucherController
 * @package App\Http\Controllers\API
 */

class AccountVoucherAPIController extends AppBaseController
{
    /** @var  AccountVoucherRepository */
    private $accountVoucherRepository;

    public function __construct(AccountVoucherRepository $accountVoucherRepo)
    {
        $this->accountVoucherRepository = $accountVoucherRepo;
    }

    /**
     * Display a listing of the AccountVoucher.
     * GET|HEAD /accountVouchers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $accountVouchers = $this->accountVoucherRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($accountVouchers->toArray(), 'Account Vouchers retrieved successfully');
    }


    // Get Voucher List With Pagination
    public function getVoucherList(Request $request) {
        $columns = ['sl', 'vdate', 'vcode', 'vtype_value', 'global_note'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

//        return $request->all();
        $query = AccountVoucher::orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereDate('vdate', 'like', '%' .$searchValue. '%');
                $query->orWhere('vcode', 'like', '%' .$searchValue. '%');
                $query->orWhere('vnumber', 'like', '%' .$searchValue. '%');
            });
        }

        $fiscal_years = $query->paginate($length);
        $return_data    = [
            'data' => $fiscal_years,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Voucher Data retrieved successfully');
    }

    /**
     * Store a newly created AccountVoucher in storage.
     * POST /accountVouchers
     *
     * @param CreateAccountVoucherAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountVoucherAPIRequest $request)
    {
        $fiscal_year = FiscalYear::where('id', $request->get('fiscal_year_id'))->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;
        $this->validate($request, [
            'fiscal_year_id' => 'required',
            'cost_center_id' => 'required',
            'vtype_id' => 'required',
            'vcode' => 'required|unique:account_vouchers,vcode',
            'vdate' => 'required|date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date,
            'global_note' => 'required'

        ]);

        $cost_center_id     = $request->get('cost_center_id');
        $global_note    = $request->get('global_note');

        $inputs = [
            'vcode' => $request->get('vcode'),
            'cost_center_id'    => $cost_center_id,
            'vnumber'   => $request->get('vnumber'),
            'vtype_id'  => $request->get('vtype_id'),
            'vtype_value'   => $request->get('vtype_value'),
            'payment_type'  => $request->get('payment_type') ?? null,
            'cheque_no' => $request->get('cheque_no') ?? null,
            'cheque_date'   => $request->get('cheque_date') ?? null,
            'fiscal_year_id'    => $request->get('fiscal_year_id'),
            'vdate' => $request->get('vdate'),
            'global_note'   => $global_note,
            'modified_item' => 0,
        ];


        $acc_transaction_data = json_decode($request->get('transaction_items'));

        $acc_transaction_items = (object) $acc_transaction_data;

        $tran_inputs    = array();
        $supplier_ledger_transaction = array();
        $ledger_transaction_data = array();
        $customer_ledger_transaction_data = array();

        $total_debit_amount = 0;
        $total_credit_amount = 0;

        if(count($acc_transaction_data) > 0) {
            $i = 0;
            foreach ($acc_transaction_items as $acc_transaction_item) {
                if(count($acc_transaction_item->account_items) > 0) {
                    $j = 0;
                    foreach ($acc_transaction_item->account_items as $account_item) {
                        if(($account_item->debit != 0 && $account_item->debit != '') || ($account_item->credit != 0 && $account_item->credit != '')) {

                            $ledger_data    = explode('___', $account_item->ledger_id);
                            $ledger_id = $ledger_data[0];
                            $ledger_code = $ledger_data[1];
                            $vaccount_type = ($account_item->debit != 0 && $account_item->debit != '') ? 'dr' : 'cr';
                            $reference_data = explode('___', $acc_transaction_item->account_items[0]->ledger_id);
                            $reference_ledger_id = $reference_data[0];
                            $reference_ledger_code = $reference_data[1];

                            if($j == 0) {
                                $reference_id = NULL;
                            }else{
                                $reference_id = $reference_ledger_code;
                            }

                            $tran_inputs[] = new AccountVoucherTransaction([
                                'cost_center_id'    => $cost_center_id,
                                'vaccount_type'   => $vaccount_type,
                                'ledger_id' => $ledger_id,
                                'ledger_code' => $ledger_code,
                                'debit' => $account_item->debit,
                                'credit'    => $account_item->credit,
                                'reference_id'  => $reference_id,
                                'transaction_sl'    => $i + 1,
                                'voucher_note'  => ($account_item->note != "") ? $account_item->note : $global_note,
                                'created_at'    => date("Y-m-d H:i:s"),
                                'updated_at'    => date("Y-m-d H:i:s"),
                            ]);

                            $total_debit_amount += $account_item->debit ? $account_item->debit : 0;
                            $total_credit_amount += $account_item->credit ? $account_item->credit : 0;


                            if( $request->get('vtype_value')== 'payment' && $account_item->debit != 0 && $account_item->debit != "") {
                                $account_ledger = AccountLedger::find($ledger_id);
                                if($account_ledger && $account_ledger->supplier_payable) {
//                                    $supplier_ledger_transaction[]  = [
//                                        'supplier'  => $account_ledger->supplier_payable,
//                                        'amount'    => $account_item->debit,
//                                    ];
                                    $ledger_transaction_data[]  = [
                                        'supplier'  => $account_ledger->supplier_payable,
                                        'amount'    => $account_item->debit,
                                    ];
                                }
                            }


                            if( $request->get('vtype_value')== 'receipt' && $account_item->credit != 0 && $account_item->credit != "") {
                                $account_ledger = AccountLedger::find($ledger_id);
                                if($account_ledger && $account_ledger->customer_receivable) {
//                                    $supplier_ledger_transaction[]  = [
//                                        'supplier'  => $account_ledger->supplier_payable,
//                                        'amount'    => $account_item->debit,
//                                    ];
                                    $customer_ledger_transaction_data[]  = [
                                        'customer'  => $account_ledger->customer_receivable,
                                        'amount'    => $account_item->credit,
                                    ];
                                }
                            }
                        }
                        $j++;
                    }
                }
                $i++;
            }
        }

//        return $tran_inputs;
//        return $customer_ledger_transaction_data;
//        return $this->supplierLedgerEntry($supplier_ledger_transaction);


        if($total_debit_amount != $total_credit_amount) {
            return $this->sendError('Debit and Credit must be same amount!');
        }

        if(count($tran_inputs) == 0) {
            return $this->sendError('Transaction items do not found!');
        }

        DB::beginTransaction();
        try{
            $accountVoucher = $this->accountVoucherRepository->create($inputs);
            $voucherTransactions = $accountVoucher->account_voucher_transactions()->saveMany($tran_inputs);
//            $supplier_ledger_save_ids   = $this->supplierLedgerEntry($supplier_ledger_transaction);


            // For Supplier Ledger
            $supplier_ledger_save_ids = array();
            $supplier_test_array    = [];
            if(count($ledger_transaction_data) > 0) {
                for($i = 0; $i < count($ledger_transaction_data); $i++) {

                    $supplier_id    = $ledger_transaction_data[$i]['supplier']->id;
                    $payment_amount    = $ledger_transaction_data[$i]['amount'];
                    $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'desc')->first();
                    if(empty($supplier_ledger)) {
                        $supplier_opening_balance = 0;
                    }else{
                        $supplier_opening_balance = $supplier_ledger->closing_balance;
                    }

                    $supplier_closing_balance = $supplier_opening_balance - $payment_amount;
                    $supplier_ledger_inputs = [
                        'supplier_id'   => $supplier_id,
                        'transaction_type'  => 'VBP',
                        'opening_balance'   => $supplier_opening_balance,
                        'payment_amount'   => $payment_amount,
                        'closing_balance'   => $supplier_closing_balance,
                        'transaction_date'  => date("Y-m-d"),
                        'created_at'  => date("Y-m-d H:i:s"),
                        'updated_at'  => date("Y-m-d H:i:s"),
                    ];

//                $supplier_test_array[] = $supplier_ledger_inputs;
                    $suppler_ledger_save    = SupplierLedger::insertGetId($supplier_ledger_inputs);
                    $supplier_ledger_save_ids[]  = $suppler_ledger_save;
                }

            }
            if(count($supplier_ledger_save_ids) > 0) {
                $supplier_ledger_update = SupplierLedger::whereIn('id', $supplier_ledger_save_ids)->update(['voucher_id' => $accountVoucher->id]);
            }

            // Supplier Ledger End


            // For Customer Ledger
            $customer_ledger_save_ids   = [];
            if(count($customer_ledger_transaction_data) > 0) {
                for($i = 0; $i < count($customer_ledger_transaction_data); $i++) {

                    $customer_id    = $customer_ledger_transaction_data[$i]['customer']->id;
                    $receive_amount    = $customer_ledger_transaction_data[$i]['amount'];
                    $customer_ledger = CustomerLedger::where('customer_id', $customer_id)->orderBy('id', 'desc')->first();
                    if(empty($customer_ledger)) {
                        $customer_opening_balance = 0;
                    }else{
                        $customer_opening_balance = $customer_ledger->closing_balance;
                    }

                    $customer_closing_balance = $customer_opening_balance - $receive_amount;
                    $customer_ledger_inputs = [
                        'customer_id'   => $customer_id,
                        'transaction_type'  => 'VBR',
                        'opening_balance'   => $customer_opening_balance,
                        'payment_receive_amount'   => $receive_amount,
                        'closing_balance'   => $customer_closing_balance,
                        'transaction_date'  => date("Y-m-d"),
                        'note'  => "Receive Payment",
                        'created_at'  => date("Y-m-d H:i:s"),
                        'updated_at'  => date("Y-m-d H:i:s"),
                    ];


                    $customer_ledger_save    = CustomerLedger::insertGetId($customer_ledger_inputs);
                    $customer_ledger_save_ids[]  = $customer_ledger_save;
                }

            }
            if(count($customer_ledger_save_ids) > 0) {
                $customer_ledger_update = CustomerLedger::whereIn('id', $customer_ledger_save_ids)->update(['voucher_id' => $accountVoucher->id]);
            }

            // Customer Ledger End

            $ledger_items = array();
            $total_debit_amount = 0;
            $total_credit_amount = 0;
            if($accountVoucher->account_voucher_transactions) {
                foreach ($accountVoucher->account_voucher_transactions as $av_transaction) {
                    $ledger_items[] = [
                        'ledger_head'   => $av_transaction->account_ledgers->ledger_name,
                        'ledger_code'   => $av_transaction->ledger_code,
                        'debit_amount'  => $av_transaction->debit,
                        'credit_amount' => $av_transaction->credit,
                        'cost_center_name' => $av_transaction->cost_centers ? $av_transaction->cost_centers->center_name : 'N/A',
                        'line_note' => $av_transaction->voucher_note
                    ];

                    $total_debit_amount += (float) $av_transaction->debit;
                    $total_credit_amount += (float) $av_transaction->credit;
                }
            }

            $return_data    = [
                'id'    => $accountVoucher->id,
                'vcode'  => $accountVoucher->vcode,
                'vtype_id' => $accountVoucher->vtype_id,
                'vtype_name'    => $accountVoucher->entry_types->name ?? 'Auto',
                'vdate' => $accountVoucher->vdate,
                'global_note'   => $accountVoucher->global_note,
                'print_date'    => date("Y-m-d H:i:s"),
                'ledger_items'  => $ledger_items,
                'total_debit_amount'    => $total_debit_amount,
                'total_credit_amount'   => $total_credit_amount,

            ];

            DB::commit();
            return $this->sendResponse($return_data, 'Account Voucher saved successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Display the specified AccountVoucher.
     * GET|HEAD /accountVouchers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountVoucher $accountVoucher */
        $accountVoucher = AccountVoucher::with(['account_voucher_transactions'])->find($id);

        if (empty($accountVoucher)) {
            return $this->sendError('Account Voucher not found');
        }

        $ledger_items = array();
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        if($accountVoucher->account_voucher_transactions) {
            foreach ($accountVoucher->account_voucher_transactions as $av_transaction) {
                $ledger_items[] = [
                    'ledger_head'   => $av_transaction->account_ledgers->ledger_name,
                    'ledger_code'   => $av_transaction->ledger_code,
                    'debit_amount'  => $av_transaction->debit,
                    'credit_amount' => $av_transaction->credit,
                    'cost_center_name' => $av_transaction->cost_centers ? $av_transaction->cost_centers->center_name : 'N/A',
                    'line_note' => $av_transaction->voucher_note
                ];

                $total_debit_amount += (float) $av_transaction->debit;
                $total_credit_amount += (float) $av_transaction->credit;
            }
        }

        $return_data    = [
            'id'    => $accountVoucher->id,
            'vcode'  => $accountVoucher->vcode,
            'vtype_id' => $accountVoucher->vtype_id,
            'vtype_name'    => $accountVoucher->entry_types->name ?? 'Auto',
            'vdate' => $accountVoucher->vdate,
            'global_note'   => $accountVoucher->global_note,
            'print_date'    => date("Y-m-d H:i:s"),
            'ledger_items'  => $ledger_items,
            'total_debit_amount'    => $total_debit_amount,
            'total_credit_amount'   => $total_credit_amount,

        ];

        return $this->sendResponse($return_data, 'Account Voucher retrieved successfully');
    }

    public function edit($id) {
        /** @var AccountVoucher $accountVoucher */
        $accountVoucher = AccountVoucher::with(['account_voucher_transactions'])->find($id);

        if (empty($accountVoucher)) {
            return $this->sendError('Account Voucher not found');
        }

        $ledger_items = array();
        $total_debit_amount = 0;
        $total_credit_amount = 0;
        if($accountVoucher->account_voucher_transactions) {
            foreach ($accountVoucher->account_voucher_transactions as $av_transaction) {
                $ledger_items[] = [
                    'id'    => $av_transaction->id,
                    'ventry_type'   => '',
                    'cost_center_id'   => $av_transaction->cost_center_id,
                    'ledger_type'   => $av_transaction->vaccount_type,
                    'ledger_id'   => $av_transaction->ledger_id.'___'.$av_transaction->ledger_code,
                    'debit'  => number_format((float) $av_transaction->debit, 2, '.', ''),
                    'credit' => number_format((float) $av_transaction->credit, 2, '.', ''),
                    'note' => $av_transaction->note,
                    'balance'   => '0.00',
                    'is_old'    => true,
                ];

                $total_debit_amount += (float) $av_transaction->debit;
                $total_credit_amount += (float) $av_transaction->credit;
            }
        }

        $return_data    = [
            'id'    => $accountVoucher->id,
            'vcode'  => $accountVoucher->vcode,
            'vnumber'  => $accountVoucher->vnumber,
            'vtype_id' => $accountVoucher->vtype_id,
            'vtype_value' => $accountVoucher->vtype_value,
            'fiscalyear_id' => $accountVoucher->fiscal_year_id,
            'vdate' => $accountVoucher->vdate,
            'global_note'   => $accountVoucher->global_note,
            'account_items'  => $ledger_items,
            'total_debit_amount'    => number_format((float) $total_debit_amount, 2, '.', ''),
            'total_credit_amount'   => number_format((float) $total_credit_amount, 2, '.', ''),

        ];

        return $this->sendResponse($return_data, 'Account Voucher retrieved successfully');
    }

    /**
     * Update the specified AccountVoucher in storage.
     * PUT/PATCH /accountVouchers/{id}
     *
     * @param int $id
     * @param UpdateAccountVoucherAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountVoucherAPIRequest $request)
    {

        $fiscal_year = FiscalYear::where('id', $request->get('fiscal_year_id'))->first();
        $start_date = $fiscal_year->start_date;
        $end_date   = $fiscal_year->end_date;
        $this->validate($request, [
            'fiscal_year_id' => 'required',
            'vtype_id' => 'required',
            'vcode' => 'required|unique:account_vouchers,vcode,'.$id,
            'vnumber'   => 'required|unique:account_vouchers,vnumber,'.$id,
            'vdate' => 'required|date|after_or_equal:'.$start_date.'|before_or_equal:'.$end_date,

        ]);

        $accountVoucher = $this->accountVoucherRepository->find($id);

        if (empty($accountVoucher)) {
            return $this->sendError('Account Voucher not found');
        }

        $inputs = [
            'vcode' => $request->get('vcode'),
            'vnumber'   => $request->get('vnumber'),
            'vtype_id'  => $request->get('vtype_id'),
            'vtype_value'   => $request->get('vtype_value'),
            'fiscal_year_id'    => $request->get('fiscal_year_id'),
            'vdate' => $request->get('vdate'),
            'global_note'   => $request->get('global_note')
        ];

        $acc_transaction_items = (object) json_decode($request->get('transaction_items'));

        DB::beginTransaction();
        try{
            $tran_inputs    = array();
            foreach($acc_transaction_items as $acc_tran_item) {
                if(($acc_tran_item->debit != 0 && $acc_tran_item->debit != '') || ($acc_tran_item->credit != 0 && $acc_tran_item->credit != '')) {

                    $ledger_data    = explode('___', $acc_tran_item->ledger_id);
                    $ledger_id = $ledger_data[0];
                    $ledger_code = $ledger_data[1];

                    if($acc_tran_item->is_old && $acc_tran_item->id != '') {

                        $acc_item_update    = [
                            'cost_center_id'    => $acc_tran_item->cost_center_id,
                            'vaccount_type'   => $acc_tran_item->ledger_type,
                            'ledger_id' => $ledger_id,
                            'ledger_code' => $ledger_code,
                            'debit' => $acc_tran_item->debit,
                            'credit'    => $acc_tran_item->credit,
                            'voucher_note'  => $acc_tran_item->note,
                            'balance'   => $acc_tran_item->balance,
                        ];

                        $acc_transaction = AccountVoucherTransaction::find($acc_tran_item->id);
                        $acc_transaction_update = $acc_transaction->update($acc_item_update);

                    }else{
                        $tran_inputs[] = new AccountVoucherTransaction([
                            'cost_center_id'    => $acc_tran_item->cost_center_id,
                            'vaccount_type'   => $acc_tran_item->ledger_type,
                            'ledger_id' => $ledger_id,
                            'ledger_code' => $ledger_code,
                            'debit' => $acc_tran_item->debit,
                            'credit'    => $acc_tran_item->credit,
                            'voucher_note'  => $acc_tran_item->note,
                            'balance'   => $acc_tran_item->balance,
                            'created_at'    => date("Y-m-d H:i:s"),
                            'updated_at'    => date("Y-m-d H:i:s"),
                        ]);
                    }

                }
            }

            $accountVoucher = $this->accountVoucherRepository->update($inputs, $id);

            if(count($tran_inputs) > 0) {
                $voucherTransactions = $accountVoucher->account_voucher_transactions()->saveMany($tran_inputs);
            }
            DB::commit();
            return $this->sendResponse($accountVoucher->toArray(), 'Account Voucher updated successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Remove the specified AccountVoucher from storage.
     * DELETE /accountVouchers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountVoucher $accountVoucher */
        $accountVoucher = $this->accountVoucherRepository->find($id);

        if (empty($accountVoucher)) {
            return $this->sendError('Account Voucher not found');
        }

        DB::beginTransaction();
        try{
            $accountVoucher->account_voucher_transactions()->delete();
            $accountVoucher->delete();
            DB::commit();
            return $this->sendSuccess('Account Voucher deleted successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    public function getVoucherCode(Request $request)  {
        $company_id = checkCompanyId($request);
        $voucher_type = $request->get('vtype');
        if ($voucher_type != '') {
            $entry_data = EntryType::where('label', $voucher_type)->where('company_id', $company_id)->first();
            $voucher_code = $this->returnVoucherCode($voucher_type);

            $return_data = [
                'voucher_type_id' => $entry_data->id,
                'voucher_code' => $voucher_code
            ];
        }else{
            $return_data = [
                'voucher_type_id' => '',
                'voucher_code' => ''
            ];
        }

        return $this->sendResponse($return_data, "Code retrieve successfully");
    }


    protected function supplierLedgerEntry($ledger_transaction_data = array())
    {
        $supplier_ledger_ids = array();
        $supplier_test_array    = [];
        if(count($ledger_transaction_data) > 0) {
            for($i = 0; $i < count($ledger_transaction_data); $i++) {

                $supplier_id    = $ledger_transaction_data[$i]['supplier']->id;
                $payment_amount    = $ledger_transaction_data[$i]['amount'];
                $supplier_ledger = SupplierLedger::where('supplier_id', $supplier_id)->orderBy('id', 'desc')->first();
                if(empty($supplier_ledger)) {
                    $supplier_opening_balance = 0;
                }else{
                    $supplier_opening_balance = $supplier_ledger->closing_balance;
                }

                $supplier_closing_balance = $supplier_opening_balance - $payment_amount;
                $supplier_ledger_inputs = [
                    'supplier_idss'   => $supplier_id,
                    'transaction_type'  => 'VBP',
                    'opening_balance'   => $supplier_opening_balance,
                    'payment_amount'   => $payment_amount,
                    'closing_balance'   => $supplier_closing_balance,
                    'transaction_date'  => date("Y-m-d"),
                ];

//                $supplier_test_array[] = $supplier_ledger_inputs;
                $suppler_ledger_save    = SupplierLedger::create($supplier_ledger_inputs);
                $supplier_ledger_ids[]  = $suppler_ledger_save->id;
            }

        }
        return $supplier_ledger_ids;
    }
}
