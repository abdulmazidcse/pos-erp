<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierAPIRequest;
use App\Http\Requests\API\UpdateSupplierAPIRequest;
use App\Http\Resources\SupplierResource;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class SupplierController
 * @package App\Http\Controllers\API
 */

class SupplierAPIController extends AppBaseController
{
    /** @var  SupplierRepository */
    private $supplierRepository;

    public function __construct(SupplierRepository $supplierRepo)
    {
        $this->supplierRepository = $supplierRepo;
    }

    /**
     * Display a listing of the Supplier.
     * GET|HEAD /suppliers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $suppliers = $this->supplierRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $return_suppliers = SupplierResource::collection($suppliers);

        return $this->sendResponse($return_suppliers, 'Suppliers retrieved successfully');
    }


    public function getSupplierList(Request $request)
    {
        $columns = ['name', 'phone', 'email', 'address', 'status'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = Supplier::select('suppliers.*', 'pl.ledger_name as supplier_payable_account', 'dl.ledger_name as supplier_discount_account', 'adl.ledger_name as supplier_advance_account')
            ->leftJoin('account_ledgers as pl', 'pl.id', 'suppliers.payable_ledger_id')
            ->leftJoin('account_ledgers as dl', 'dl.id', 'suppliers.discount_ledger_id')
            ->leftJoin('account_ledgers as adl', 'adl.id', 'suppliers.advance_ledger_id')
            ->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('phone', 'like', '%' .$searchValue. '%');
                $query->orWhere('email', 'like', '%' .$searchValue. '%');
            });
        }

        $suppliers = $query->paginate($length);
        $return_data    = [
            'data' => $suppliers,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Suppliers retrieved successfully');
    }

    /**
     * Store a newly created Supplier in storage.
     * POST /suppliers
     *
     * @param CreateSupplierAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateSupplierAPIRequest $request)
    {
        $pmd_rules = ($request->payment_terms_conditions == 1) ? 'required' : 'sometimes';
        $cp_rules = ($request->payment_terms_conditions == '2') ? 'required' : 'sometimes';

        $this->validate($request, [
            'name'  => 'required',
            'address'   => 'required',
            'payment_terms_conditions'   => 'required',
            'supply_schedule'   => 'required',
            'payment_matured_days' => $pmd_rules,
            'commission_percent'    => $cp_rules,
            'logo_image'      => 'sometimes|image|max:2000|mimes:jpeg,png,jpg,gif,svg',
            'supplier_payable_account' => 'required',
//            'supplier_discount_account' => 'required',
//            'supplier_advance_account' => 'required',

        ]);


//        return response()->json($request->all());

//        $input = $request->except(['supplier_payable_account', 'supplier_discount_account', 'supplier_advance_account']);
        $input = $request->except(['supplier_payable_account']);

        if($request->payment_matured_days == '') {
            $input['payment_matured_days']  = 0;
        }
        if($request->commission_percent == '') {
            $input['commission_percent']  = 0;
        }

        if($request->hasFile('logo_image')){
            $file = $request->file('logo_image');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'supplier', 'supplier_logo_');
            $input['logo_image'] = $fileName;
        }

        $account_default_setting = AccountDefaultSetting::first();
        $supplier_payable_account_type = AccountType::where('id', $account_default_setting->supplier_payable_account_type)->first();
//        $supplier_discount_account_type = AccountType::where('id', $account_default_setting->supplier_discount_account_type)->first();
//        $supplier_advance_account_type = AccountType::where('id', $account_default_setting->supplier_advance_payment_account_type)->first();

        $payable_account_inputs = [
            'ledger_code'   => $this->returnAccountCode($supplier_payable_account_type->id, 'dtype'),
            'ledger_name'   => $request->get('supplier_payable_account'),
            'type_id'   => $supplier_payable_account_type->parent_type_id,
            'detail_type_id'    => $supplier_payable_account_type->id,
        ];

//        $discount_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($supplier_discount_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('supplier_payable_account'),
//            'type_id'   => $supplier_discount_account_type->parent_type_id,
//            'detail_type_id'    => $supplier_discount_account_type->id,
//        ];
//
//        $advance_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($supplier_advance_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('supplier_advance_account'),
//            'type_id'   => $supplier_advance_account_type->parent_type_id,
//            'detail_type_id'    => $supplier_advance_account_type->id,
//        ];

        DB::beginTransaction();
        try {
            $payable_account_save   = AccountLedger::create($payable_account_inputs);
//            $discount_account_save   = AccountLedger::create($discount_account_inputs);
//            $advance_account_save   = AccountLedger::create($advance_account_inputs);

            $input['payable_ledger_id'] = $payable_account_save->id;
//            $input['discount_ledger_id'] = $discount_account_save->id;
//            $input['advance_ledger_id'] = $advance_account_save->id;
            $supplier = $this->supplierRepository->create($input);
//            $supplier->update([
//                'payable_ledger_id' => $payable_account_save->id,
//                'discount_ledger_id' => $discount_account_save->id,
//                'advance_ledger_id' => $advance_account_save->id,
//            ]);

            DB::commit();
            return $this->sendResponse($supplier->toArray(), 'Supplier saved successfully');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    // Supplier Add Via PO page
    public function storeSupplier(CreateSupplierAPIRequest $request)
    {
        $this->validate($request, [
            'supplier_type_id'  => 'required',
            'name'   => 'required',
            'phone'   => 'required'
        ]);

        $input = [
            'name'  => $request->get('name'),
            'type_id'   => $request->get('supplier_type_id'),
            'phone'     => $request->get('phone'),
            'email'     => $request->get('email'),
            'payment_terms_conditions'  => 4
        ];

        $account_default_setting = AccountDefaultSetting::first();
        $supplier_payable_account_type = AccountType::where('id', $account_default_setting->supplier_payable_account_type)->first();
//        $supplier_discount_account_type = AccountType::where('id', $account_default_setting->supplier_discount_account_type)->first();
//        $supplier_advance_account_type = AccountType::where('id', $account_default_setting->supplier_advance_payment_account_type)->first();

        $payable_account_inputs = [
            'ledger_code'   => $this->returnAccountCode($supplier_payable_account_type->id, 'dtype'),
            'ledger_name'   => $request->get('name'),
            'type_id'   => $supplier_payable_account_type->parent_type_id,
            'detail_type_id'    => $supplier_payable_account_type->id,
        ];

//        $discount_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($supplier_discount_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('name'). " Discount",
//            'type_id'   => $supplier_discount_account_type->parent_type_id,
//            'detail_type_id'    => $supplier_discount_account_type->id,
//        ];
//
//        $advance_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($supplier_advance_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('name'). " Advance",
//            'type_id'   => $supplier_advance_account_type->parent_type_id,
//            'detail_type_id'    => $supplier_advance_account_type->id,
//        ];

        DB::beginTransaction();
        try {
            $payable_account_save   = AccountLedger::create($payable_account_inputs);
//            $discount_account_save   = AccountLedger::create($discount_account_inputs);
//            $advance_account_save   = AccountLedger::create($advance_account_inputs);

            $input['payable_ledger_id'] = $payable_account_save->id;
//            $input['discount_ledger_id'] = $discount_account_save->id;
//            $input['advance_ledger_id'] = $advance_account_save->id;
            $supplier = $this->supplierRepository->create($input);

            DB::commit();
            return $this->sendResponse($supplier->toArray(), 'Supplier Added successfully');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified Supplier.
     * GET|HEAD /suppliers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $return_supplier = new SupplierResource($supplier);

        return $this->sendResponse($return_supplier, 'Supplier retrieved successfully');
    }

    /**
     * Update the specified Supplier in storage.
     * PUT/PATCH /suppliers/{id}
     *
     * @param int $id
     * @param UpdateSupplierAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSupplierAPIRequest $request)
    {
        $pmd_rules = ($request->payment_terms_conditions == 1) ? 'required' : 'sometimes';
        $cp_rules = ($request->payment_terms_conditions == 2) ? 'required' : 'sometimes';

        $this->validate($request, [
            'name'  => 'required',
            'address'   => 'required',
            'payment_terms_conditions'   => 'required',
            'supply_schedule'   => 'required',
            'payment_matured_days' => $pmd_rules,
            'commission_percent'    => $cp_rules,
            'logo_image'      => 'sometimes|image|max:2000|mimes:jpeg,png,jpg,gif,svg',
            'supplier_payable_account' => 'required',
//            'supplier_discount_account' => 'required',
//            'supplier_advance_account' => 'required',

        ]);

//        $input = $request->except(['supplier_payable_account', 'supplier_discount_account', 'supplier_advance_account']);
        $input = $request->except(['supplier_payable_account']);

        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $old_logo_image = $supplier->logo_image;
        if($request->hasFile('logo_image')){
            $file = $request->file('logo_image');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'supplier', 'supplier_logo_');
            $input['logo_image'] = $fileName;

            $this->removeFile($old_logo_image, 'supplier');
        }

        // Account Added
        $account_default_setting = AccountDefaultSetting::first();
        $supplier_payable_account_type = AccountType::where('id', $account_default_setting->supplier_payable_account_type)->first();
//        $supplier_discount_account_type = AccountType::where('id', $account_default_setting->supplier_discount_account_type)->first();
//        $supplier_advance_account_type = AccountType::where('id', $account_default_setting->supplier_advance_payment_account_type)->first();

        $supplier_payable_ledger    = $supplier->payable_accounts;
//        $supplier_discount_ledger   = $supplier->discount_accounts;
//        $supplier_advance_ledger    = $supplier->advance_accounts;


//        return $this->sendResponse($supplier_payable_ledger, "Test");
        DB::beginTransaction();
        try {
            // Payable Ledger
//            if(!empty($supplier_payable_ledger)) {
//                $supplier_payable_ledger->update(['ledger_name' => $request->get('supplier_payable_account')]);
//
//            }

            if(empty($supplier_payable_ledger)) {
                $supplier_payable_inputs    = [
                    'ledger_code'   => $this->returnAccountCode($supplier_payable_account_type->id, 'dtype'),
                    'ledger_name'   => $request->get('supplier_payable_account'),
                    'type_id'   => $supplier_payable_account_type->parent_type_id,
                    'detail_type_id'    => $supplier_payable_account_type->id,
                ];

                $payable_account_save   = AccountLedger::create($supplier_payable_inputs);

                $input['payable_ledger_id'] = $payable_account_save->id;
            }

//            // Discount Ledger
//            if(!empty($supplier_discount_ledger)) {
//                $supplier_discount_ledger->update(['ledger_name' => $request->get('supplier_discount_account')]);
//
//            }else{
//                $supplier_discount_inputs    = [
//                    'ledger_code'   => $this->returnAccountCode($supplier_discount_account_type->id, 'dtype'),
//                    'ledger_name'   => $request->get('supplier_discount_account'),
//                    'type_id'   => $supplier_discount_account_type->parent_type_id,
//                    'detail_type_id'    => $supplier_discount_account_type->id,
//                ];
//
//                $discount_account_save   = AccountLedger::create($supplier_discount_inputs);
//
//                $input['discount_ledger_id'] = $discount_account_save->id;
//            }
//
//            // Advance Ledger
//            if(!empty($supplier_advance_ledger)) {
//                $supplier_advance_ledger->update(['ledger_name' => $request->get('supplier_advance_account')]);
//
//            }else{
//                $supplier_advance_inputs    = [
//                    'ledger_code'   => $this->returnAccountCode($supplier_advance_account_type->id, 'dtype'),
//                    'ledger_name'   => $request->get('supplier_advance_account'),
//                    'type_id'   => $supplier_advance_account_type->parent_type_id,
//                    'detail_type_id'    => $supplier_advance_account_type->id,
//                ];
//
//                $advance_account_save   = AccountLedger::create($supplier_advance_inputs);
//
//                $input['advance_ledger_id'] = $advance_account_save->id;
//            }

            $supplier = $this->supplierRepository->update($input, $id);

            DB::commit();
            return $this->sendResponse($supplier->toArray(), 'Supplier updated successfully');
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

    }

    /**
     * Remove the specified Supplier from storage.
     * DELETE /suppliers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $supplier_ledgers = $supplier->supplier_ledgers;

        $account_transactions = $supplier->payable_accounts->account_transactions;

        if(count($supplier_ledgers) == 0 && count($account_transactions) == 0) {
            $supplier->payable_accounts->delete();
            $supplier->delete();
        }else{
            return $this->sendError("This supplier can't be delete because it's already use!");
        }

        return $this->sendSuccess('Supplier deleted successfully');
    }
}
