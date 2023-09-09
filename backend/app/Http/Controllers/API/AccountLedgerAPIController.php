<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountLedgerAPIRequest;
use App\Http\Requests\API\UpdateAccountLedgerAPIRequest;
use App\Http\Resources\AccountLedgerResource;
use App\Http\Resources\ChartOfAccountExportResource;
use App\Models\AccountType;
use App\Models\AccountLedger;
use App\Repositories\AccountLedgerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Exports\ChatOfAccountsExport; 
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

/**
 * Class AccountLedgerController
 * @package App\Http\Controllers\API
 */

class AccountLedgerAPIController extends AppBaseController
{
    /** @var  AccountLedgerRepository */
    private $accountLedgerRepository;

    public function __construct(AccountLedgerRepository $accountLedgerRepo)
    {
        $this->accountLedgerRepository = $accountLedgerRepo;
    }

    /**
     * Display a listing of the AccountLedger.
     * GET|HEAD /accountLedgers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
//        $accountLedgers = $this->accountLedgerRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );

        $accountLedgers = AccountLedger::with(['account_types'])->orderBy('ledger_code', 'ASC')->get();

        $accountLedgers = AccountLedgerResource::collection($accountLedgers);

        return $this->sendResponse($accountLedgers, 'Account Ledgers retrieved successfully');
    }



    public function getLedgerList(Request $request)
    {
        $columns = ['sl','ledger_name', 'ledger_code', 'name', 'type_name', 'status'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $sortKey = $request->input('sortKey');
//        return [
//            'dir' => $dir,
//            'sortKey' => $sortKey,
//        ];
        $searchValue = $request->input('search');

//        $query = AccountLedger::with(['account_types'])->orderBy($columns[$column], $dir);
        $query = AccountLedger::with(['account_types'])
        ->when($sortKey == "account_types.name", function($query) use($dir){
            return pleaseSortMe($query, $dir, AccountType::select('account_types.type_name')
                ->whereColumn('account_types.id', 'account_ledgers.detail_type_id')
                ->take(1));
        })
        ->when($sortKey == "account_types.parent_types.name", function($query) use($dir){
            return pleaseSortMe($query, $dir, AccountType::select('parent_account_types.type_name')
                ->join('account_types as parent_account_types', 'parent_account_types.id', '=', 'account_types.parent_type_id')
                ->whereColumn('account_types.id', 'account_ledgers.detail_type_id')
                ->take(1));
        })
        ->when(!in_array($sortKey, ["account_types.name", "account_types.parent_types.name"]), function($query) use($dir, $columns, $column){
            return $query->orderBy($columns[$column], $dir);
        });

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('ledger_code', 'like', '%' .$searchValue. '%');
                $query->orWhere('ledger_name', 'like', '%' .$searchValue. '%');
            });
        }

        $accountLedgers = AccountLedgerResource::collection($query->paginate($length))->resource;
        $return_data    = [
            'data' => $accountLedgers,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Accounts Ledgers retrieved successfully');
    }

    // For Chart Of Accounts
    public function getChartOfAccounts(AccountLedger $accountLedger)
    {

        $accounts = $accountLedger->getChartOfAccountsList('');

        $return_data    = [
            'accounts'    => $accounts
        ];

        return $this->sendResponse($return_data, 'Chart of Accounts Retrieve Successfully!');

    }
    

    public function getChartOfAccountsOption(AccountLedger $accountLedger)
    {

        $accounts = $accountLedger->getChartOfAccountsList('');

        $return_data    = [
            'accounts'    => $accounts
        ];

        return $this->sendResponse($return_data, 'Chart of Accounts Retrieve Successfully!');

    }

    public function getChartOfAccountsOnlyLedgerOption(AccountLedger $accountLedger) {
        $accounts = $accountLedger->getChartOfAccountOptions('');

        $return_data    = [
            'accounts'  => $accounts
        ];

        return $this->sendResponse($return_data, 'Ledger Account Retrieve Successfully!');
    }

    /**
     * Store a newly created AccountLedger in storage.
     * POST /accountLedgers
     *
     * @param CreateAccountLedgerAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountLedgerAPIRequest $request)
    {
        $this->validate($request, [
            'type_id'  => 'required',
            'detail_type_id'  => 'required',
//            'parent_id'  => 'required',
            'ledger_code'   => 'required|unique:account_ledgers,ledger_code',
            'ledger_name'   => 'required|unique:account_ledgers,ledger_name,NULL,id,detail_type_id,'.$request->get('detail_type_id'),
        ]);

        $input = $request->all();
        $accountLedger = $this->accountLedgerRepository->create($input);

        return $this->sendResponse($accountLedger->toArray(), 'Account Ledger saved successfully');
    }

    /**
     * Display the specified AccountLedger.
     * GET|HEAD /accountLedgers/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountLedger $accountLedger */
        $accountLedger = $this->accountLedgerRepository->find($id);

        if (empty($accountLedger)) {
            return $this->sendError('Account Ledger not found');
        }

        return $this->sendResponse($accountLedger->toArray(), 'Account Ledger retrieved successfully');
    }

    /**
     * Update the specified AccountLedger in storage.
     * PUT/PATCH /accountLedgers/{id}
     *
     * @param int $id
     * @param UpdateAccountLedgerAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountLedgerAPIRequest $request)
    {
        $this->validate($request, [
            'type_id'  => 'required',
            'detail_type_id'  => 'required',
//            'parent_id'  => 'required',
            'ledger_code'   => 'required|unique:account_ledgers,ledger_code,'.$id,
            'ledger_name'   => 'required|unique:account_ledgers,ledger_name,'.$id.',id,detail_type_id,'.$request->get('detail_type_id'),
        ]);

        $input = $request->all();

//        return $input;
        /** @var AccountLedger $accountLedger */
        $accountLedger = $this->accountLedgerRepository->find($id);

        if (empty($accountLedger)) {
            return $this->sendError('Account Ledger not found');
        }

        $accountLedger = $this->accountLedgerRepository->update($input, $id);

        return $this->sendResponse($accountLedger->toArray(), 'AccountLedger updated successfully');
    }

    /**
     * Remove the specified AccountLedger from storage.
     * DELETE /accountLedgers/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountLedger $accountLedger */
        $accountLedger = $this->accountLedgerRepository->find($id);
        $account_transactions = $accountLedger->account_transactions;

//        return $account_transactions;

        if (empty($accountLedger)) {
            return $this->sendError('Account Ledger not found');
        }

//        $accountLedger->delete();
        if(count($account_transactions) > 0) {
            return $this->sendError("This ledger can't be delete, because is used for account transactions");
        }else {
            $accountLedger->forceDelete();
        }

        return $this->sendSuccess('Account Ledger deleted successfully');
    }

    // For Get Parent Account
    public function getParentAccounts()
    {
        $accountParents = AccountLedger::where('is_control_transaction', 0)->orderBy('ledger_code')->get();
        if(empty($accountParents)) {
            return $this->sendError('Parent Account Not Found!');
        }
        return $this->sendResponse($accountParents, "Parent Accounts retrieve successfully!");
    }
    // For Account Code
    public function getAccountCode(Request $request)
    {
        $reference_id = $request->get('reference_id');
        $type = $request->get('reference_type');

        //return [$group_id, $parent_id];
        $ledger_code = $this->returnAccountCode($reference_id, $type);

        $return_data    = [
            'account_code'   => $ledger_code
        ];

        return $this->sendResponse($return_data, 'Account Code retrieve successfully!');
    }

    public function chartOfAccountExportCollection(AccountLedger $accountLedger){  

        $accounts = $accountLedger->getChartOfAccountsList('');  
        
        dd( $accounts);

        // Now $data contains the array of data 
        $return_data = ChartOfAccountExportResource::collection($accounts); 

        return $this->sendResponse($return_data,'');
    } 
  
    public function getChartOfAccountExcelExport(AccountLedger $accountLedger)
    { 
        // Get the data array from the JSON response
        $returnData  = $accountLedger->getChartOfAccountsList('');  

        $customHeadings = [ ['Supplier Name: '],[]];
        $columns = ['Sl','code', 'name','account_type','account_type_name','children']; 

        // Create an instance of the export class with the data 
        $margeRangeOne = 'A1:H1';
        $margeRangeTwo = 'A2:H2';
        $export = new ChatOfAccountsExport($returnData);

        // Generate and download the Excel file
        return Excel::download($export, 'chart-of-accounts.xlsx'); 

    }
}
