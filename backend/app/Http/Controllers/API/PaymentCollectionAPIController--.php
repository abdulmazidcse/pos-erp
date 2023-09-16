<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentCollectionAPIRequest;
use App\Http\Requests\API\UpdatePaymentCollectionAPIRequest;
use App\Models\Agro\CustomerLedger;
use App\Models\PaymentCollection;
use App\Models\Sale;
use App\Repositories\PaymentCollectionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

        // Sales, CustomerLedger, PaymentCollection, 
        foreach ($jsonDecode as $key => $item) {  
            $saleItem  = Sale::wit(['salesItems'])->find($item['inv_id']);
            $payments[] = new PaymentCollection([
                'sale_id'       => $item['inv_id'],
                'amount'        => $item['pay_amount'],
                'card_reference_no' => $item['card_reference_code'] ?? NULL,
                'bank_id'       => $item['bank_id'] ?? NULL,
                'paying_by'     => $input['payment_type'] ?? NULL,
                'payment_note'  => $item['payment_note'] ?? NULL,
                'wallet_id'     => $item['wallet_id'] ?? NULL, 
                'transaction_no' => $item['transaction_no'] ?? NULL,
            ]);   
            $input['collection_amount'] += $item['pay_amount'];             
            
            // $total_item_discount += ($value['quantity'] * $value['discount']);
            // $total_item_vat += $value['tax'];
            // $total_item_mrp_amount += ($value['quantity'] * $product->mrp_price);
            // $total_item_cost_amount += ($value['quantity'] * $product->cost_price);
        } 

        $customer_id     = $input['customer_id'];
        $receive_amount  = $input['collection_amount'];
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
            'payment_receive_amount' => $receive_amount,
            'closing_balance'   => $customer_closing_balance,
            'transaction_date'  => date("Y-m-d"),
            'note'  => "Receive Payment",
            'created_at'  => date("Y-m-d H:i:s"),
            'updated_at'  => date("Y-m-d H:i:s"),
        ];
        CustomerLedger::insertGetId($customer_ledger_inputs); 
 
        // if(count($customer_ledger_save) > 0) {
        //     CustomerLedger::whereIn('id', $customer_ledger_save)->update(['voucher_id' => $accountVoucher->id]);
        // }
        // Account voucher Transestion
        // $sales_transaction_data = [
        //     'fiscal_year_id'    => $fiscal_year->id,
        //     'total_discount'    => $total_discount,
        //     'total_vat_amount'  => $total_vat_amount,
        //     'total_item_mrp_amount' => $total_item_mrp_amount,
        //     'total_cogs_amount'    => $total_item_cost_amount,
        //     'total_cash_amount' => (($total_item_mrp_amount + $total_vat_amount) - $total_discount),
        //     'sale_status'   => $request->status,
        //     'paid_amount'   => $request->paid_amount,
        // ];
        // $account_transaction = $this->saleAccountTransaction($sales_transaction_data);
        // $sale->update(['voucher_id' => $account_transaction->id]);

        // Account voucher Transestion
        
        dd( json_decode($input['invoice_items'])); 

        $paymentCollection = $this->paymentCollectionRepository->create($input);

        return $this->sendResponse($paymentCollection->toArray(), 'Payment Collection saved successfully');
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
