<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyAPIRequest;
use App\Http\Requests\API\UpdateCompanyAPIRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Validator;
use Response;
use Image;
/**
 * Class CompanyController
 * @package App\Http\Controllers\API
 */

class CompanyAPIController extends AppBaseController
{
    /** @var  CompanyRepository */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepo)
    {
        $this->companyRepository = $companyRepo;
    }

    /**
     * Display a listing of the Company.
     * GET|HEAD /companies
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 
        if (in_array('Super Admin', $roles)) { 
            $company_id  = $request->input('company_id');
        }else{
            $company_id  = $user->company_id;
        }    
        $companies = $this->companyRepository->allQuery()->when($company_id, function($q, $company_id){
            return $q->where('id', $company_id);
        })->get();

        $return_data    = CompanyResource::collection($companies);

        return $this->sendResponse($return_data, 'Companies retrieved successfully');
    }

    /**
     * Store a newly created Company in storage.
     * POST /companies
     *
     * @param CreateCompanyAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyAPIRequest $request)
    { 
        $this->validate($request, [
            'name'      => 'required|unique:companies,name',
            'address'   => 'required',
            'contact_person_name'   => 'required',
            'contact_person_number' => 'required',
            'status'                => 'required', 
            'logo'      => 'sometimes|image|max:2000|mimes:jpeg,png,jpg,gif,svg',
        ], [
            'name.unique' => 'This Company Already Exists',
        ]);

        $input = $request->all();

        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $fileExt    = $file->getClientOriginalExtension();
            $fileName   = $this->uploadFile($file, 'company', 'company_logo_'); 
            $input['logo'] = $fileName;
        }
        $company = $this->companyRepository->create($input);
        return $this->sendResponse($company->toArray(), 'Company saved successfully');
    }

    /**
     * Display the specified Company.
     * GET|HEAD /companies/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Company $company */
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $return_data    = new CompanyResource($company);
        return $this->sendResponse($return_data, 'Company retrieved successfully');
    }

    /**
     * Update the specified Company in storage.
     * PUT/PATCH /companies/{id}
     *
     * @param int $id
     * @param UpdateCompanyAPIRequest $request
     *
     * @return Response
     */
    public function update(UpdateCompanyAPIRequest $request, $id)
    { 
        $this->validate($request, [
            'name'          => 'required|unique:companies,name,'.$id,
            'address'   => 'required',
            'contact_person_name'   => 'required',
            'contact_person_number' => 'required',
            'status'                => 'required',
            'logo'      => 'sometimes|image|max:2000|mimes:jpeg,png,jpg,gif,svg',
        ], [
            'name.unique' => 'This Company Already Exists',
        ]);

        $input = $request->all();

        /** @var Company $company */
        $company = $this->companyRepository->find($id);
        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $old_logo = $company->logo;
        if($request->hasFile('logo')){
            $file = $request->file('logo');
            $fileName   = $this->uploadFile($file, 'company', 'company_');

            $input['logo'] = $fileName;

            $this->removeFile($old_logo, 'company');
        }

        $company = $this->companyRepository->update($input, $id);

        return $this->sendResponse($company->toArray(), 'Company updated successfully');
    }

    /**
     * Remove the specified Company from storage.
     * DELETE /companies/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Company $company */
        $company = $this->companyRepository->find($id);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $company->delete();

        return $this->sendSuccess('Company deleted successfully');
    }
}
