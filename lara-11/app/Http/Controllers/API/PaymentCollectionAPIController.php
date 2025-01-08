<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentCollectionAPIRequest;
use App\Http\Requests\API\UpdatePaymentCollectionAPIRequest; 
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountVoucher;
use App\Models\AccountVoucherTransaction;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\EntryType;
use App\Models\PaymentCollection;
use App\Models\Sale;
use App\Repositories\PaymentCollectionRepository;
use App\Repositories\AccountVoucherRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\API\SaleAPIController;
use Response;


/**
 * Class PaymentCollectionController
 * @package App\Http\Controllers\API
 */

class PaymentCollectionAPIController extends AppBaseController
{
    /** @var  PaymentCollectionRepository */
    private $paymentCollectionRepository;
 

    public function __construct(PaymentCollectionRepository $paymentCollectionRepo)
    {
        $this->paymentCollectionRepository = $paymentCollectionRepo; 
    }

    /**
     * Display a listing of the PaymentCollection.
     * GET|HEAD /paymentCollections
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $paymentCollections = $this->paymentCollectionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($paymentCollections->toArray(), 'Payment Collections retrieved successfully');
    }

    /**
     * Store a newly created PaymentCollection in storage.
     * POST /paymentCollections
     *
     * @param CreatePaymentCollectionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePaymentCollectionAPIRequest $request)
    {
        $input = $request->all();
        $input['collection_amount'] = 0 ;
        $jsonDecode = json_decode($input['invoice_items']); 
        $invoiceIds = collect($jsonDecode)->pluck('invoice_number')->toArray();   //Convert to "INV00123000005,INV00123000026"
        $stringInvNumber = implode(', ', $invoiceIds ) ;       

        // PaymentCollection, Sales, Accountvoucher, CustomerLedger
        foreach ($jsonDecode as $key => $item) {  
            $input['collection_amount'] += $item->pay_amount; 
            // Collection Insert End 
            $payments = new PaymentCollection();   
            $payments->sale_id       = $item->inv_id;
            $payments->paying_by     = $input['payment_type'] ?? NULL;
            $payments->amount        = $item->pay_amount;
            $payments->card_reference_no = $item->card_reference_code ?? NULL;
            $payments->bank_id       = $item->bank_id ?? NULL;
            $payments->payment_note  = $item->payment_note ?? NULL;
            $payments->wallet_id     = $item->wallet_id ?? NULL;
            $payments->transaction_no = $item->transaction_no ?? NULL; 
            $payments->save();
            // Collection Insert End 

            // // Sales Update start
            $saleItem  = Sale::with(['salesItems'])->find($item->inv_id); 
            if( $saleItem->grand_total == $item->pay_amount){
                $paidStatus = 'paid';
            } else if( $saleItem->grand_total > $item->pay_amount){
                if( ($item->pay_amount > 0) && ($saleItem->grand_total > $item->pay_amount)){
                    $paidStatus = 'partial';                    
                }else{
                    $paidStatus = 'due'; 
                }
            } 
            $saleItem->collection_amount = $item->pay_amount;
            $saleItem->paid_amount = $item->pay_amount;
            $saleItem->status = $paidStatus; 
            $saleItem->save();
            // Sales Update End  
        } 
        $input['global_note'] = 'Payment Receive for - '.$stringInvNumber;
        // Account voucher Start
        $voucher = $this->accountsVoucher($input); 
        foreach ($jsonDecode as $key => $item) { 
            $this->customerLedger( $input['customer_id'], 0, $item->inv_id, $voucher->id, $item->pay_amount );
        } 

        return $this->sendResponse($invoiceIds, 'Payment Collection saved successfully');
    }

    protected function customerLedger( $customer_id, $grand_total, $inv_id, $voucher_id, $receive_amount ){ 
        $customer_ledger = CustomerLedger::where('customer_id', $customer_id)->orderBy('id', 'desc')->first();
            
        if(empty($customer_ledger)) {
            $customer_opening_balance = 0;
        }else{
            $customer_opening_balance = $customer_ledger->closing_balance;
        }
        $customer_closing_balance = $customer_opening_balance - $receive_amount;
        $customer_ledger_data = new CustomerLedger();
        $customer_ledger_data->customer_id = $customer_id;
        $customer_ledger_data->voucher_id = $voucher_id;
        $customer_ledger_data->sale_id = $inv_id;
        $customer_ledger_data->transaction_type = 'sale';
        $customer_ledger_data->note = 'Due Collection';
        $customer_ledger_data->sales_amount = $grand_total;
        $customer_ledger_data->payment_receive_amount = $receive_amount;
        $customer_ledger_data->opening_balance = $customer_opening_balance;
        $customer_ledger_data->closing_balance = $customer_closing_balance;
        $customer_ledger_data->transaction_date = date("Y-m-d");
        $customer_ledger_data->save();
    }

    protected function accountsVoucher($input){  
        $vtype_value = 'receipt';
        $voucher_code = $this->returnVoucherCode($vtype_value);
        $entry_data = EntryType::where('label', $vtype_value)->first();
        $inputs = [
            'vcode' => $voucher_code,
            'invoice_type' => 'SALE',
            'cost_center_id'    => $input['cost_center_id'], 
            'vtype_id'  => $entry_data->id,
            'vtype_value'   => $vtype_value,
            'payment_type'  => $input['payment_type'] ?? null,
            'cheque_no' => $input['cheque_no'] ?? null,
            'cheque_date'   => $input['cheque_date'] ?? null,
            'fiscal_year_id'    => $input['fiscal_year_id'],
            'vdate' => $input['vdate'],
            'global_note'   => $input['global_note'],
            'modified_item' => 0,
        ]; 
        $accountVoucher = AccountVoucher::create($inputs); 

        $customer = Customer::with(['receivable_accounts'])->find($input['customer_id']); 
        $company_id = checkCompanyId($request); 
        $account_default_setting = AccountDefaultSetting::where('company_id',  $company_id)->first();
        $cash_ledger    = getLedgerAccountById($accountDefaultSetting->cash_in_hand_account); // dr assets 
        $account_receivable_ledger    = $customer->receivable_accounts ? getLedgerAccountById($customer->receivable_accounts->id) : getLedgerAccountById($accountDefaultSetting->account_reaceivable_ledger); // cr assets 

        $transactions    = [ 
            new AccountVoucherTransaction([
                'cost_center_id'    => $input['cost_center_id'],
                'vaccount_type'   => 'dr',
                'ledger_id' => $cash_ledger->id,
                'ledger_code' => $cash_ledger->ledger_code,
                'debit' => $input['collection_amount'],
                'credit'    => 0,
                'reference_id'  => null,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]),
            new AccountVoucherTransaction([
                'cost_center_id'    => $input['cost_center_id'],
                'vaccount_type'   => 'cr',
                'ledger_id' => $account_receivable_ledger->id,
                'ledger_code' => $account_receivable_ledger->ledger_code,
                'debit' => 0,
                'credit'    => $input['collection_amount'],
                'reference_id'  => $cash_ledger->ledger_code,
                'transaction_sl'    => 1,
                'voucher_note'  => null,
                'balance'   => 0,
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]), 
        ];
        $accountVoucher->account_voucher_transactions()->saveMany($transactions);  
        return $accountVoucher;
    }
    

    /**
     * Display the specified PaymentCollection.
     * GET|HEAD /paymentCollections/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PaymentCollection $paymentCollection */
        $paymentCollection = $this->paymentCollectionRepository->find($id);

        if (empty($paymentCollection)) {
            return $this->sendError('Payment Collection not found');
        }

        return $this->sendResponse($paymentCollection->toArray(), 'Payment Collection retrieved successfully');
    }

    /**
     * Update the specified PaymentCollection in storage.
     * PUT/PATCH /paymentCollections/{id}
     *
     * @param int $id
     * @param UpdatePaymentCollectionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePaymentCollectionAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentCollection $paymentCollection */
        $paymentCollection = $this->paymentCollectionRepository->find($id);

        if (empty($paymentCollection)) {
            return $this->sendError('Payment Collection not found');
        }

        $paymentCollection = $this->paymentCollectionRepository->update($input, $id);

        return $this->sendResponse($paymentCollection->toArray(), 'PaymentCollection updated successfully');
    }

    /**
     * Remove the specified PaymentCollection from storage.
     * DELETE /paymentCollections/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PaymentCollection $paymentCollection */
        $paymentCollection = $this->paymentCollectionRepository->find($id);

        if (empty($paymentCollection)) {
            return $this->sendError('Payment Collection not found');
        }

        $paymentCollection->delete();

        return $this->sendSuccess('Payment Collection deleted successfully');
    }
}
