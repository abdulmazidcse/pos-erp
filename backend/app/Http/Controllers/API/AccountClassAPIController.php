<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccountClassAPIRequest;
use App\Http\Requests\API\UpdateAccountClassAPIRequest;
use App\Models\AccountClass;
use App\Repositories\AccountClassRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

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
        $accountClasses = $this->accountClassRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $return_data   = [
            'account_classes' => $accountClasses->toArray(),
        ];

        return $this->sendResponse($return_data, 'Account Classes retrieved successfully');
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
        $this->validate($request, [
           'name' => 'required|unique:account_classes,name',
           'code' => 'required|unique:account_classes,code'
        ]);

        $input = $request->all();

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

        $this->validate($request, [
            'name' => 'required|unique:account_classes,name,'.$id,
            'code' => 'required|unique:account_classes,code,'.$id
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

        $class_code = $this->returnAccountClassCode($prefix);

        $return_data = [
            'group_code'    => $class_code
        ];

        return $this->sendResponse($return_data, 'Account Group Code retrieve successfully');
    }
}
