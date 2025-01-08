<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerLedgerAPIRequest;
use App\Http\Requests\API\UpdateCustomerLedgerAPIRequest;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Repositories\CustomerLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Exports\Export; 
use Maatwebsite\Excel\Facades\Excel;
use Response;

/**
 * Class CustomerLedgerController
 * @package App\Http\Controllers\API
 */

class CustomerLedgerAPIController extends AppBaseController 
{
    /** @var  CustomerLedgerRepository */
    private $customerLedgerRepository;

    public function __construct(CustomerLedgerRepository $customerLedgerRepo)
    {
        $this->customerLedgerRepository = $customerLedgerRepo;
    }

    /**
     * Display a listing of the CustomerLedger.
     * GET|HEAD /customerLedgers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required',
            'start_date' => 'required',
            'end_date'   => 'required',
        ]);

        $customer_id = $request->get('customer_id');
        $from_date  = date("Y-m-d", strtotime($request->get('start_date')));
        $to_date  = date("Y-m-d", strtotime($request->get('end_date'))); 

        $customer = Customer::find($customer_id);

        $customer_ledger = CustomerLedger::where('customer_id', $customer_id)->whereDate('transaction_date', '<', $from_date)->orderBy('id', 'DESC')->first();

        $customer_ledgers = CustomerLedger::where('customer_id', $customer_id)->whereBetween('transaction_date', [$from_date, $to_date])->get();

        $return_data    = [
            'opening_balance' => $customer_ledger->closing_balance ?? 0,
            'customer_ledgers' => $customer_ledgers,
            'from_date' => $from_date,
            'to_date'   => $to_date,
            'customer_name' => $customer->name ?? 'N/A'
        ];

        return $this->sendResponse($return_data, 'Customer Ledgers retrieved successfully');
    } 
 
    public function customerLedgersExport(Request $request){ 

        // Call the index method to retrieve the JSON response
        $response = $this->index($request);

        // Get the data array from the JSON response
        $data = $response->getData();

        // Now $data contains the array of data
        $returnData = $data->data->customer_ledgers;  
        
        $customer_id = $request->get('customer_id');
        $from_date  = date("Y-m-d", strtotime($request->get('start_date')));
        $to_date  = date("Y-m-d", strtotime($request->get('end_date'))); 
        $customer = Customer::find($customer_id);

        $customHeadings = [ ['Customer Name :'.$customer->name],[$from_date .' To '.$to_date]];
        $columns = ['Sl', 'transaction_date','opening_balance','sales_amount','payment_receive_amount', 'closing_balance']; 

        // Create an instance of the export class with the data
        $margeRangeOne = 'A1:F1';
        $margeRangeTwo = 'A2:F2';
        $export = new Export($returnData, $columns, $customHeadings,  $margeRangeOne, $margeRangeTwo);

        // Generate and download the Excel file
        return Excel::download($export, 'customer_ledgers.xlsx');
    }

    /**
     * Store a newly created CustomerLedger in storage.
     * POST /customerLedgers
     *
     * @param CreateCustomerLedgerAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerLedgerAPIRequest $request)
    {
        $input = $request->all();

        $customerLedger = $this->customerLedgerRepository->create($input);

        return $this->sendResponse($customerLedger->toArray(), 'Customer Ledger saved successfully');
    }

    /**
     * Display the specified CustomerLedger.
     * GET|HEAD /customerLedgers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CustomerLedger $customerLedger */
        $customerLedger = $this->customerLedgerRepository->find($id);

        if (empty($customerLedger)) {
            return $this->sendError('Customer Ledger not found');
        }

        return $this->sendResponse($customerLedger->toArray(), 'Customer Ledger retrieved successfully');
    }

    /**
     * Update the specified CustomerLedger in storage.
     * PUT/PATCH /customerLedgers/{id}
     *
     * @param int $id
     * @param UpdateCustomerLedgerAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerLedgerAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerLedger $customerLedger */
        $customerLedger = $this->customerLedgerRepository->find($id);

        if (empty($customerLedger)) {
            return $this->sendError('Customer Ledger not found');
        }

        $customerLedger = $this->customerLedgerRepository->update($input, $id);

        return $this->sendResponse($customerLedger->toArray(), 'CustomerLedger updated successfully');
    }

    /**
     * Remove the specified CustomerLedger from storage.
     * DELETE /customerLedgers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CustomerLedger $customerLedger */
        $customerLedger = $this->customerLedgerRepository->find($id);

        if (empty($customerLedger)) {
            return $this->sendError('Customer Ledger not found');
        }

        $customerLedger->delete();

        return $this->sendSuccess('Customer Ledger deleted successfully');
    }
}
