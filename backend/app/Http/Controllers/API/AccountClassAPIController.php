<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountClassAPIRequest;
use App\Http\Requests\API\UpdateAccountClassAPIRequest;
use App\Models\AccountClass;
use App\Repositories\AccountClassRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Illuminate\Validation\Rule;

/**
 * Class AccountClassController
 * @package App\Http\Controllers\API
 */

class AccountClassAPIController extends AppBaseController
{
    /** @var  AccountClassRepository */
    private $accountClassRepository;

    public function __construct(AccountClassRepository $accountClassRepo)
    {
        $this->accountClassRepository = $accountClassRepo;
    }

    /**
     * Display a listing of the AccountClass.
     * GET|HEAD /accountClasses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $accountClasses = $this->accountClassRepository->all(
        //     $request->except(['skip', 'limit']),
        //     $request->get('skip'),
        //     $request->get('limit')
        // );

        // $return_data   = [
        //     'account_classes' => $accountClasses->toArray(),
        // ];

        // return $this->sendResponse($return_data, 'Account Classes retrieved successfully');

        $company_id = checkCompanyId($request);
        $accountClasses = $this->accountClassRepository->allQuery()->when($company_id, function($q, $company_id){
            return $q->where('company_id', $company_id);
        })->get();

        $return_data   = [
            'account_classes' => $accountClasses->toArray(),
        ];

        return $this->sendResponse($return_data, 'Companies retrieved successfully');
    }

    /**
     * Store a newly created AccountClass in storage.
     * POST /accountClasses
     *
     * @param CreateAccountClassAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountClassAPIRequest $request)
    { 
        $company_id = checkCompanyId($request);
        $this->validate($request, [
        //    'name' => 'required|unique:account_classes,name',
           'name' => [
                'required', 
                Rule::unique('account_classes')->where(function ($query) use ($company_id) {
                    return $query->where('company_id', $company_id);
                }),
            ],
           'code' => [
                'required', 
                Rule::unique('account_classes')->where(function ($query) use ($company_id) {
                    return $query->where('company_id', $company_id);
                }),
            ]
        ]);

        $input = $request->all();
        $input['company_id'] = $company_id;

        $accountClass = $this->accountClassRepository->create($input);

        return $this->sendResponse($accountClass->toArray(), 'Account Class saved successfully');
    }

    /**
     * Display the specified AccountClass.
     * GET|HEAD /accountClasses/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccountClass $accountClass */
        $accountClass = $this->accountClassRepository->find($id);

        if (empty($accountClass)) {
            return $this->sendError('Account Class not found');
        }

        return $this->sendResponse($accountClass->toArray(), 'Account Class retrieved successfully');
    }

    /**
     * Update the specified AccountClass in storage.
     * PUT/PATCH /accountClasses/{id}
     *
     * @param int $id
     * @param UpdateAccountClassAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountClassAPIRequest $request)
    {
        $company_id = checkCompanyId($request);
        $this->validate($request, [
            // 'name' => 'required|unique:account_classes,name,'.$id,
            'name' => [
                'required',
                Rule::unique('account_classes')->where(function ($query) use ($company_id) {
                    return $query->where('company_id', $company_id);
                })->ignore($id), // Replace $id with the actual id of the record being updated
            ],
           'code' => [
                'required', 
                Rule::unique('account_classes')->where(function ($query) use ($company_id) {
                    return $query->where('company_id', $company_id);
                })->ignore($id),
            ]
        ]);

        $input = $request->all();

        /** @var AccountClass $accountClass */
        $accountClass = $this->accountClassRepository->find($id);

        if (empty($accountClass)) {
            return $this->sendError('Account Class not found');
        }

        $accountClass = $this->accountClassRepository->update($input, $id);

        return $this->sendResponse($accountClass->toArray(), 'AccountClass updated successfully');
    }

    /**
     * Remove the specified AccountClass from storage.
     * DELETE /accountClasses/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccountClass $accountClass */
        $accountClass = $this->accountClassRepository->find($id);

        if (empty($accountClass)) {
            return $this->sendError('Account Class not found');
        }

        $accountClass->delete();

        return $this->sendSuccess('Account Class deleted successfully');
    }


    // get Account class code
    public function getAccountClassCode(Request $request) {

        $prefix = $request->get('prefix');
        $company_id = checkCompanyId($request);

        $class_code = $this->returnAccountClassCode($company_id, $prefix);

        $return_data = [
            'group_code'    => $class_code
        ];

        return $this->sendResponse($return_data, 'Account Group Code retrieve successfully');
    }
}
