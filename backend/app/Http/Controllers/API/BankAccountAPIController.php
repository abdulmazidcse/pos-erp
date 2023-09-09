<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankAccountAPIRequest;
use App\Http\Requests\API\UpdateBankAccountAPIRequest;
use App\Models\AccountDefaultSetting;
use App\Models\AccountLedger;
use App\Models\AccountType;
use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class AccountController
 * @package App\Http\Controllers\API
 */

class BankAccountAPIController extends AppBaseController
{
    /** @var  BankAccountRepository */
    private $bankAccountRepository;

    public function __construct(BankAccountRepository $bankAccountRepo)
    {
        $this->bankAccountRepository = $bankAccountRepo;
    }

    /**
     * Display a listing of the Account.
     * GET|HEAD /accounts
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $accounts = $this->bankAccountRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($accounts->toArray(), 'Accounts retrieved successfully');
    }

    public function getList(Request $request)
    {
        $columns = ['account_no', 'bank_name', 'initial_balance', 'current_balance', 'note', 'created_at'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = BankAccount::select('bank_accounts.*', 'baal.ledger_name as bank_asset_account', 'blal.ledger_name as bank_loan_account', 'bcal.ledger_name as bank_charge_account', 'biel.ledger_name as bank_interest_expense_account', 'biil.ledger_name as bank_interest_income_account')
            ->leftJoin('account_ledgers as baal', 'baal.id', 'bank_accounts.bank_asset_account')
            ->leftJoin('account_ledgers as blal', 'blal.id', 'bank_accounts.bank_loan_account')
            ->leftJoin('account_ledgers as bcal', 'bcal.id', 'bank_accounts.bank_charge_account')
            ->leftJoin('account_ledgers as biel', 'biel.id', 'bank_accounts.bank_interest_expense_account')
            ->leftJoin('account_ledgers as biil', 'biil.id', 'bank_accounts.bank_interest_income_account')
            ->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('account_no', 'like', '%' .$searchValue. '%');
                $query->orWhere('bank_name', 'like', '%' .$searchValue. '%');
                $query->orWhere('initial_balance', 'like', '%' .$searchValue. '%');
                $query->orWhere('current_balance', 'like', '%' .$searchValue. '%');
            });
        }

        $bank_accounts = $query->paginate($length);
        $return_data    = [
            'data' => $bank_accounts,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Accounts retrieved successfully');
    }

    /**
     * Store a newly created Account in storage.
     * POST /accounts
     *
     * @param CreateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBankAccountAPIRequest $request)
    {
        $this->validate($request, [
            'account_no'    => 'required|unique:bank_accounts,account_no',
            'bank_name'  => 'required',
            'company_id'    => 'sometimes',

            'bank_asset_account'    => 'required',
//            'bank_loan_account'    => 'required',
//            'bank_charge_account'    => 'required',
//            'bank_interest_expense_account'    => 'required',
//            'bank_interest_income_account'    => 'required',
        ]);

//        $input = $request->except(['bank_asset_account', 'bank_loan_account', 'bank_charge_account', 'bank_interest_expense_account', 'bank_interest_income_account']);
        $input = $request->except(['bank_asset_account']);

        $input['initial_balance']   = 0;
        $input['current_balance']   = 0;


        $account_default_setting = AccountDefaultSetting::first();

        $bank_asset_account_type = AccountType::where('id', $account_default_setting->bank_account_asset_type)->first();
//        $bank_loan_account_type = AccountType::where('id', $account_default_setting->bank_loan_account_liability_type)->first();
//        $bank_charge_account_type = AccountType::where('id', $account_default_setting->bank_charge_account_expense_type)->first();
//        $bank_interest_expense_account_type = AccountType::where('id', $account_default_setting->bank_loan_interest_expense_type)->first();
//        $bank_interest_income_account_type = AccountType::where('id', $account_default_setting->bank_interest_income_type)->first();

        $bank_asset_account_inputs = [
            'ledger_code'   => $this->returnAccountCode($bank_asset_account_type->id, 'dtype'),
            'ledger_name'   => $request->get('bank_asset_account').'-'.$request->get('account_no'),
            'type_id'   => $bank_asset_account_type->parent_type_id,
            'detail_type_id'    => $bank_asset_account_type->id,
        ];

//        $bank_loan_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($bank_loan_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('bank_loan_account'),
//            'type_id'   => $bank_loan_account_type->parent_type_id,
//            'detail_type_id'    => $bank_loan_account_type->id,
//        ];
//
//        $bank_charge_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($bank_charge_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('bank_charge_account'),
//            'type_id'   => $bank_charge_account_type->parent_type_id,
//            'detail_type_id'    => $bank_charge_account_type->id,
//        ];
//
//        $bank_interest_expense_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($bank_interest_expense_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('bank_interest_expense_account'),
//            'type_id'   => $bank_interest_expense_account_type->parent_type_id,
//            'detail_type_id'    => $bank_interest_expense_account_type->id,
//        ];
//
//        $bank_interest_income_account_inputs = [
//            'ledger_code'   => $this->returnAccountCode($bank_interest_income_account_type->id, 'dtype'),
//            'ledger_name'   => $request->get('bank_interest_income_account'),
//            'type_id'   => $bank_interest_income_account_type->parent_type_id,
//            'detail_type_id'    => $bank_interest_income_account_type->id,
//        ];

//        return response()->json([$bank_asset_account_inputs, $bank_loan_account_inputs, $bank_charge_account_inputs, $bank_interest_income_account_inputs, $bank_interest_expense_account_inputs]);

        DB::beginTransaction();
        try{

            $bank_asset_account   = AccountLedger::create($bank_asset_account_inputs);
//            $bank_loan_account   = AccountLedger::create($bank_loan_account_inputs);
//            $bank_charge_account   = AccountLedger::create($bank_charge_account_inputs);
//            $bank_interest_expense_account   = AccountLedger::create($bank_interest_expense_account_inputs);
//            $bank_interest_income_account   = AccountLedger::create($bank_interest_income_account_inputs);

            $input['bank_asset_account'] = $bank_asset_account->id;
//            $input['bank_loan_account'] = $bank_loan_account->id;
//            $input['bank_charge_account'] = $bank_charge_account->id;
//            $input['bank_interest_expense_account'] = $bank_interest_expense_account->id;
//            $input['bank_interest_income_account'] = $bank_interest_income_account->id;


            $account = $this->bankAccountRepository->create($input);
            DB::commit();

            return $this->sendResponse($account->toArray(), 'Account saved successfully');

        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified Account.
     * GET|HEAD /accounts/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BankAccount $account */
        $account = $this->bankAccountRepository->find($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        return $this->sendResponse($account->toArray(), 'Account retrieved successfully');
    }

    /**
     * Update the specified Account in storage.
     * PUT/PATCH /accounts/{id}
     *
     * @param int $id
     * @param UpdateBankAccountAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBankAccountAPIRequest $request)
    {
        $this->validate($request, [
            'account_no'    => 'required|unique:bank_accounts,account_no,'.$id,
            'bank_name'  => 'required',
            'company_id'    => 'sometimes',

            'bank_asset_account'    => 'required',
//            'bank_loan_account'    => 'required',
//            'bank_charge_account'    => 'required',
//            'bank_interest_expense_account'    => 'required',
//            'bank_interest_income_account'    => 'required',
        ]);

//        $input = $request->except(['bank_asset_account', 'bank_loan_account', 'bank_charge_account', 'bank_interest_expense_account', 'bank_interest_income_account']);
        $input = $request->except(['bank_asset_account']);


        $input['initial_balance']   = 0;
        $input['current_balance']   = 0;


        /** @var BankAccount $account */
        $account = $this->bankAccountRepository->find($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        $account_default_setting = AccountDefaultSetting::first();

        $bank_asset_account_type = AccountType::where('id', $account_default_setting->bank_account_asset_type)->first();
//        $bank_loan_account_type = AccountType::where('id', $account_default_setting->bank_loan_account_liability_type)->first();
//        $bank_charge_account_type = AccountType::where('id', $account_default_setting->bank_charge_account_expense_type)->first();
//        $bank_interest_expense_account_type = AccountType::where('id', $account_default_setting->bank_loan_interest_expense_type)->first();
//        $bank_interest_income_account_type = AccountType::where('id', $account_default_setting->bank_interest_income_type)->first();


        $bank_asset_account   = $account->bank_asset_accounts;
//        $bank_loan_account   = $account->bank_loan_accounts;
//        $bank_charge_account   = $account->bank_charge_accounts;
//        $bank_interest_expense_account   = $account->bank_interest_expense_accounts;
//        $bank_interest_income_account   = $account->bank_interest_income_accounts;


        DB::beginTransaction();
        try{
//            if($bank_asset_account) {
//                $bank_asset_account->update(['ledger_name' => $request->get('bank_asset_account')]);
//            }

            if(empty($bank_asset_account)) {

                $bank_asset_account_inputs = [
                    'ledger_code' => $this->returnAccountCode($bank_asset_account_type->id, 'dtype'),
                    'ledger_name' => $request->get('bank_asset_account'),
                    'type_id' => $bank_asset_account_type->parent_type_id,
                    'detail_type_id' => $bank_asset_account_type->id,
                ];

                $bank_asset_account_save = AccountLedger::create($bank_asset_account_inputs);
                $input['bank_asset_account']    = $bank_asset_account_save->id;
            }

//            if($bank_loan_account) {
//                $bank_loan_account->update(['ledger_name' => $request->get('bank_loan_account')]);
//            }else {
//                $bank_loan_account_inputs = [
//                    'ledger_code' => $this->returnAccountCode($bank_loan_account_type->id, 'dtype'),
//                    'ledger_name' => $request->get('bank_loan_account'),
//                    'type_id' => $bank_loan_account_type->parent_type_id,
//                    'detail_type_id' => $bank_loan_account_type->id,
//                ];
//
//                $bank_loan_account_save = AccountLedger::create($bank_loan_account_inputs);
//                $input['bank_loan_account'] = $bank_loan_account_save->id;
//            }
//
//            if($bank_charge_account) {
//                $bank_charge_account->update(['ledger_name' => $request->get('bank_charge_account')]);
//            }else {
//                $bank_charge_account_inputs = [
//                    'ledger_code' => $this->returnAccountCode($bank_charge_account_type->id, 'dtype'),
//                    'ledger_name' => $request->get('bank_charge_account'),
//                    'type_id' => $bank_charge_account_type->parent_type_id,
//                    'detail_type_id' => $bank_charge_account_type->id,
//                ];
//
//                $bank_charge_account_save = AccountLedger::create($bank_charge_account_inputs);
//                $input['bank_charge_account']   = $bank_charge_account_save->id;
//            }
//
//            if($bank_interest_expense_account) {
//                $bank_interest_expense_account->update(['ledger_name' => $request->get('bank_interest_expense_account')]);
//            }else {
//                $bank_interest_expense_account_inputs = [
//                    'ledger_code' => $this->returnAccountCode($bank_interest_expense_account_type->id, 'dtype'),
//                    'ledger_name' => $request->get('bank_interest_expense_account'),
//                    'type_id' => $bank_interest_expense_account_type->parent_type_id,
//                    'detail_type_id' => $bank_interest_expense_account_type->id,
//                ];
//
//                $bank_interest_expense_account_save = AccountLedger::create($bank_interest_expense_account_inputs);
//                $input['bank_interest_expense_account'] = $bank_interest_expense_account_save->id;
//            }
//
//            if($bank_interest_income_account) {
//                $bank_interest_income_account->update(['ledger_name' => $request->get('bank_interest_income_account')]);
//            }else {
//                $bank_interest_income_account_inputs = [
//                    'ledger_code' => $this->returnAccountCode($bank_interest_income_account_type->id, 'dtype'),
//                    'ledger_name' => $request->get('bank_interest_income_account'),
//                    'type_id' => $bank_interest_income_account_type->parent_type_id,
//                    'detail_type_id' => $bank_interest_income_account_type->id,
//                ];
//
//                $bank_interest_income_account_save = AccountLedger::create($bank_interest_income_account_inputs);
//                $input['bank_interest_income_account'] = $bank_interest_income_account_save->id;
//            }

            $account = $this->bankAccountRepository->update($input, $id);

            DB::commit();
            return $this->sendResponse($account->toArray(), 'Account updated successfully');
        }catch(\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }


    }

    /**
     * Remove the specified Account from storage.
     * DELETE /accounts/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BankAccount $account */
        $account = $this->bankAccountRepository->find($id);

        if (empty($account)) {
            return $this->sendError('Account not found');
        }

        $account->delete();

        return $this->sendSuccess('Account deleted successfully');
    }
}
