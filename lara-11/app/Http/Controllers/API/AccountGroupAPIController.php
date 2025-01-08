<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountGroupAPIRequest;
use App\Http\Requests\API\UpdateAccountGroupAPIRequest;
use App\Models\AccountGroup;
use App\Repositories\AccountGroupRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class AccountGroupController
 * @package App\Http\Controllers\API
 */

class AccountGroupAPIController extends AppBaseController
{
    /** @var  AccountGroupRepository */
    private $accountGroupRepository;

    public function __construct(AccountGroupRepository $accountGroupRepo)
    {
        $this->accountGroupRepository = $accountGroupRepo;
    }

    /**
     * Display a listing of the AccountGroup.
     * GET|HEAD /accountGroups
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $accountGroups = $this->accountGroupRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $return_data    = [
            'account_groups'    => $accountGroups->toArray()
        ];

        return $this->sendResponse($return_data, 'Account Groups retrieved successfully');
    }

    // Get Chart of Accounts
    public function getChartOfAccounts()
    {

        $accountGroups = accountGroups('');

        $return_data    = [
            'account_groups'    => $accountGroups
        ];

        return $this->sendResponse($return_data, 'Chart of Accounts Retrieve Successfully!');

    }

    public function getChartOfAccountsTest(AccountGroup $accountGroup)
    {

        $accountGroups = $accountGroup->accountGroups('');

        $return_data    = [
            'account_groups'    => $accountGroups
        ];

        return $this->sendResponse($return_data, 'Chart of Accounts Retrieve Successfully!');

    }


    /**
     * Store a newly created AccountGroup in storage.
     * POST /accountGroups
     *
     * @param CreateAccountGroupAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountGroupAPIRequest $request)
    {
        $this->validate($request, [
            'group_name'    => 'required|unique:account_groups,group_name,NULL,id,parent_id,'.$request->get('parent_id'),
        ]);

        $input = $request->all();

        $accountGroup = $this->accountGroupRepository->create($input);

        return $this->sendResponse($accountGroup->toArray(), 'Account Group saved successfully');
    }

    /**
     * Display the specified AccountGroup.
     * GET|HEAD /accountGroups/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountGroup $accountGroup */
        $accountGroup = $this->accountGroupRepository->find($id);

        if (empty($accountGroup)) {
            return $this->sendError('Account Group not found');
        }

        return $this->sendResponse($accountGroup->toArray(), 'Account Group retrieved successfully');
    }

    /**
     * Update the specified AccountGroup in storage.
     * PUT/PATCH /accountGroups/{id}
     *
     * @param int $id
     * @param UpdateAccountGroupAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountGroupAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccountGroup $accountGroup */
        $accountGroup = $this->accountGroupRepository->find($id);

        if (empty($accountGroup)) {
            return $this->sendError('Account Group not found');
        }

        $accountGroup = $this->accountGroupRepository->update($input, $id);

        return $this->sendResponse($accountGroup->toArray(), 'AccountGroup updated successfully');
    }

    /**
     * Remove the specified AccountGroup from storage.
     * DELETE /accountGroups/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountGroup $accountGroup */
        $accountGroup = $this->accountGroupRepository->find($id);

        if (empty($accountGroup)) {
            return $this->sendError('Account Group not found');
        }

        $accountGroup->delete();

        return $this->sendSuccess('Account Group deleted successfully');
    }


    public function getParentsGroup()
    {
//        $accountGroups = AccountGroup::all();
//
//        return $this->sendResponse($accountGroups->toArray(), 'Parent Group Retrieve Successfully');
        $accountGroups = '<option value="0">--- Select Group ---</option>';
        $accountGroups .= accountGroupOptions('');

        return $this->sendResponse($accountGroups, 'Parent Group Retrieve Successfully');
    }

    public function getGroupCode(Request $request)
    {
        $parent_id = $request->get('parent_id');

//        return $parent_id;

        if($parent_id != 0 && $parent_id != '') {
            $group_code = $this->returnAccountGroupCode($parent_id);
        }else{
            $group_code = $this->returnAccountGroupCode();
        }

        $return_data    = [
            'group_code'    => $group_code
        ];

        return $this->sendResponse($return_data, 'Account Group Code retrieved successfully');
    }


}
