<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerAPIRequest;
use App\Http\Requests\API\UpdateCustomerAPIRequest;
use App\Http\Resources\CustomerResource;
use App\Imports\CustomerImport;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use Response;

/**
 * Class CustomerController
 * @package App\Http\Controllers\API
 */

class CustomerAPIController extends AppBaseController
{
    /** @var  CustomerRepository */
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepository = $customerRepo;
    }

    /**
     * Display a listing of the Customer.
     * GET|HEAD /customers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $customers = $this->customerRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $data   = CustomerResource::collection($customers);

        return $this->sendResponse($data, 'Customers retrieved successfully');
    }


    public function customerList(Request $request)
    {
        $columns = ['sl','customer_code', 'name', 'phone', 'email', 'address', 'customer_group_name','customer_group_id','customer_receivable_account'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $sortKey = $request->input('sortKey');
        $searchValue = $request->input('search');

        $query = Customer::orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('customer_code', 'like', '%' .$searchValue. '%');
                $query->orWhere('phone', 'like', '%' .$searchValue. '%');
                $query->orWhere('email', 'like', '%' .$searchValue. '%');
            });
        }

        $data = $query->paginate($length);
        $customers  = CustomerResource::collection($data)->resource;
        $return_data    = [
            'data' => $customers,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Customers retrieved successfully');
    }

    /**
     * Store a newly created Customer in storage.
     * POST /customers
     *
     * @param CreateCustomerAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomerAPIRequest $request)
    {
        $this->validate($request, [
            'customer_code' => 'required|unique:customers,customer_code',
            'customer_group_id' => 'required',
            'name'  => 'required',
            'phone' => 'required|min:10|unique:customers,phone',
            'address'   => 'required',
            'discount_percent' => 'required',
            'customer_receivable_account'   => 'required',
        ]);

        $input = $request->except(['customer_receivable_account']);

        $account_default_setting = AccountDefaultSetting::first();
        $customer_receivable_account_type = AccountType::where('id', $account_default_setting->customer_receivable_account_type)->first();


        $receivable_account_inputs = [
            'ledger_code'   => $this->returnAccountCode($customer_receivable_account_type->id, 'dtype'),
            'ledger_name'   => $request->get('customer_receivable_account').' ('.$request->get('phone').')',
            'type_id'   => $customer_receivable_account_type->parent_type_id,
            'detail_type_id'    => $customer_receivable_account_type->id,
        ];

        DB::beginTransaction();
        try{

            $receivable_account_save    = AccountLedger::insertGetId($receivable_account_inputs);

            $input['receivable_ledger_id']  = $receivable_account_save;


            $customer = $this->customerRepository->create($input);

            DB::commit();
            return $this->sendResponse($customer->toArray(), 'Customer saved successfully');

        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /** Bulk Customer Uploads */
    public function customerBulkStore(Request $request, CustomerImport $customerImport)
    {
        $this->validate($request, [
            'excel_file'    => 'required',
        ]);

        $file = $request->file('excel_file');

        $import = $customerImport->import($file);

        // return response()->json($import);

        if($import){
            return $this->sendResponse($import, 'Bulk Stock successfully done!');
        }else{
            return $this->sendResponse($import, 'Something went wrong, please try again!');
        }
    }

    /**
     * Display the specified Customer.
     * GET|HEAD /customers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->find($id);

        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }

        $data   = new CustomerResource($customer);

        return $this->sendResponse($data, 'Customer retrieved successfully');
    }

    /**
     * Update the specified Customer in storage.
     * PUT/PATCH /customers/{id}
     *
     * @param int $id
     * @param UpdateCustomerAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomerAPIRequest $request)
    {
        $this->validate($request, [
            'customer_code' => 'required|unique:customers,customer_code,'.$id,
            'customer_group_id' => 'required',
            'name'  => 'required',
            'phone' => 'required|min:10|unique:customers,phone,'.$id,
            'address'   => 'required',
            'discount_percent' => 'required',
            'customer_receivable_account'   => 'required',
        ]);

        $input = $request->except(['customer_receivable_account']);

        /** @var Customer $customer */
        $customer = $this->customerRepository->find($id);

        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }

        // Account Added
        $account_default_setting = AccountDefaultSetting::first();
        $customer_receivable_account_type = AccountType::where('id', $account_default_setting->customer_receivable_account_type)->first();

        $customer_receivable_ledger    = $customer->receivable_accounts;

        DB::beginTransaction();
        try{
            // Receivable Ledger
//            if(!empty($customer_receivable_ledger)) {
//                $customer_receivable_ledger->update(['ledger_name' => $request->get('customer_receivable_account')]);
//
//            }
            if(empty($customer_receivable_ledger)) {
                $customer_receivable_inputs    = [
                    'ledger_code'   => $this->returnAccountCode($customer_receivable_account_type->id, 'dtype'),
                    'ledger_name'   => $request->get('customer_receivable_account').' ('.$request->get('phone').')',
                    'type_id'   => $customer_receivable_account_type->parent_type_id,
                    'detail_type_id'    => $customer_receivable_account_type->id,
                ];

                $receivable_account_save   = AccountLedger::insertGetId($customer_receivable_inputs);

                $input['receivable_ledger_id'] = $receivable_account_save;
            }
            $customer = $this->customerRepository->update($input, $id);

            DB::commit();
            return $this->sendResponse($customer->toArray(), 'Customer updated successfully');

        }
        catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified Customer from storage.
     * DELETE /customers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->find($id);

        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }

        $customer->delete();

        return $this->sendSuccess('Customer deleted successfully');
    }


}
