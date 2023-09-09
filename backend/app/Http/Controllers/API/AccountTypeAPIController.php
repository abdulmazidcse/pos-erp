<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountTypeAPIRequest;
use App\Http\Requests\API\UpdateAccountTypeAPIRequest;
use App\Models\AccountClass;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Repositories\AccountTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AccountTypeController
 * @package App\Http\Controllers\API
 */

class AccountTypeAPIController extends AppBaseController
{
    /** @var  AccountTypeRepository */
    private $accountTypeRepository;

    public function __construct(AccountTypeRepository $accountTypeRepo)
    {
        $this->accountTypeRepository = $accountTypeRepo;
    }

    /**
     * Display a listing of the AccountType.
     * GET|HEAD /accountTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
//        $accountTypes = $this->accountTypeRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );

        $accountTypes = AccountType::with(['account_classes', 'type_parents', 'type_children'])->orderBy('type_code')->get();


        return $this->sendResponse($accountTypes->toArray(), 'Account Types retrieved successfully');
    }



    public function getAccountTypesList(Request $request)
    {
        $columns = ['sl','type_name', 'type_code', 'parent_type', 'group_name', 'status'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $sortKey = $request->input('sortKey');
        $searchValue = $request->input('search');

//        $query = AccountLedger::with(['account_types'])->orderBy($columns[$column], $dir);
        $query = AccountType::with(['account_classes', 'type_parents', 'type_children'])
            ->when($sortKey == "parent_type", function($query) use($dir){
                return pleaseSortMe($query, $dir, AccountType::select('account_types.type_name')
                    ->whereColumn('account_types.id', 'account_types.parent_type_id')
                    ->take(1));
            })
            ->when($sortKey == "group_name", function($query) use($dir){
                return pleaseSortMe($query, $dir, AccountClass::select('account_classes.name')
                    ->whereColumn('account_classes.id', 'account_types.class_id')
                    ->take(1));
            })
            ->when(!in_array($sortKey, ["parent_type", "group_name"]), function($query) use($dir, $columns, $column){
                return $query->orderBy($columns[$column], $dir);
            });

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('type_code', 'like', '%' .$searchValue. '%');
                $query->orWhere('type_name', 'like', '%' .$searchValue. '%');
            });
        }

        $accountTypes = $query->paginate($length);
        $return_data    = [
            'data' => $accountTypes,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Accounts Types retrieved successfully');
    }


    // Type Data List
    public function getAccountTypeList($group_id = null)
    {
        if($group_id) {
            $accountTypes = AccountType::doesntHave('type_parents')->where('status', 1)->where('class_id', $group_id)->orderBy('type_name')->get();
        }else {
            $accountTypes = AccountType::where('status', 1)->orderBy('type_name')->get();
        }

        return $this->sendResponse($accountTypes, 'Account Type Return Successfully');
    }

    /**
     * Store a newly created AccountType in storage.
     * POST /accountTypes
     *
     * @param CreateAccountTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountTypeAPIRequest $request)
    {
        $this->validate($request, [
            'type_code' => 'required',
            'type_name' => 'required'
        ]);
        $class_id = $request->get('class_id');
        $parent_id = $request->get('parent_type_id') ?? 0;
        $type_name = $request->get('type_name');
        $type_code = $request->get('type_code');

        $type_name_exists = AccountType::where('class_id', $class_id)
                                        ->where('parent_type_id', $parent_id)->where('type_name', $type_name)->get();

        $type_code_exists = AccountType::where('type_code', $type_code)->get();

        if(count($type_name_exists) > 0) {
            $response = [
                'errors'    => [
                    'type_name' => ['This type name already exists!']
                ]
            ];

            return response()->json($response, 422);
        }

        if(count($type_code_exists) > 0) {
            $response = [
                'errors'    => [
                    'type_code' => ['This type code already exists!']
                ]
            ];

            return response()->json($response, 422);
        }

        $input = $request->all();
        $input['parent_type_id']    = $parent_id;

        $accountType = $this->accountTypeRepository->create($input);

        return $this->sendResponse($accountType->toArray(), 'Account Type saved successfully');
    }

    /**
     * Display the specified AccountType.
     * GET|HEAD /accountTypes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountType $accountType */
        $accountType = $this->accountTypeRepository->find($id);

        if (empty($accountType)) {
            return $this->sendError('Account Type not found');
        }

        return $this->sendResponse($accountType->toArray(), 'Account Type retrieved successfully');
    }

    /**
     * Update the specified AccountType in storage.
     * PUT/PATCH /accountTypes/{id}
     *
     * @param int $id
     * @param UpdateAccountTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountTypeAPIRequest $request)
    {
        $this->validate($request, [
            'type_code' => 'required',
            'type_name' => 'required'
        ]);
        $class_id = $request->get('class_id');
        $parent_id = $request->get('parent_type_id') ?? 0;
        $type_name = $request->get('type_name');
        $type_code = $request->get('type_code');

        $type_name_exists = AccountType::where('class_id', $class_id)
            ->where('parent_type_id', $parent_id)->where('type_name', $type_name)->where('id', '!=', $id)->get();
        $type_code_exists = AccountType::where('type_code', $type_code)->where('id', '!=', $id)->get();

        if(count($type_name_exists) > 0) {
            $response = [
                'errors'    => [
                    'type_name' => ['This type name already exists!']
                ]
            ];

            return response()->json($response, 422);
        }

        if(count($type_code_exists) > 0) {
            $response = [
                'errors'    => [
                    'type_code' => ['This type code already exists!']
                ]
            ];

            return response()->json($response, 422);
        }

        $input = $request->all();
        $input['parent_type_id']    = $parent_id;

        /** @var AccountType $accountType */
        $accountType = $this->accountTypeRepository->find($id);

        if (empty($accountType)) {
            return $this->sendError('Account Type not found');
        }

        $accountType = $this->accountTypeRepository->update($input, $id);

        return $this->sendResponse($accountType->toArray(), 'AccountType updated successfully');
    }

    /**
     * Remove the specified AccountType from storage.
     * DELETE /accountTypes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountType $accountType */
        $accountType = $this->accountTypeRepository->find($id);

        if (empty($accountType)) {
            return $this->sendError('Account Type not found');
        }

        $accountType->delete();

        return $this->sendSuccess('Account Type deleted successfully');
    }

    //
    public function getParentTypes($group_id = null)
    {
        if($group_id) {
            $accountTypes = AccountType::where('parent_type_id', 0)->where('class_id', $group_id)->where('status', 1)->get();
        }else {
            $accountTypes = AccountType::where('parent_type_id', 0)->where('status', 1)->get();
        }
        if(empty($accountTypes)) {
            return $this->sendError('Types not found!');
        }

        return $this->sendResponse($accountTypes, 'Types data retrieve successfully!');

    }

    //
    public function getChartOfAccountsOnlyDetailTypeOptions(Request $request) {

        $accountLedgerObject = new AccountLedger();


        $types = json_decode($request->types);
//        return $types;
        $accountsDetailTypes = $accountLedgerObject->getAccountDetailTypes($types, '', 1);

        return $this->sendResponse($accountsDetailTypes, 'Ledger Account Retrieve Successfully!');
    }

    //
    public function getChartOfAccountsTypeOptions(Request $request) {

        $accountLedgerObject = new AccountLedger();


        $class_id = $request->class_id;
//        return $types;
        $accountTypes = $accountLedgerObject->getOnlyAccountTypes('', $class_id, 0);

        return $this->sendResponse($accountTypes, 'Account Types Retrieve Successfully!');
    }

    //
    public function getTypesCode(Request $request) {

        $reference_id   = $request->get('reference_id');
        $type   = $request->get('reference_type');

        $type_code = $this->returnAccountTypeCode($reference_id, $type);

        return $this->sendResponse($type_code, "Type Code Retrieve Successfully");
    }
}
