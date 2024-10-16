<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFiscalYearAPIRequest;
use App\Http\Requests\API\UpdateFiscalYearAPIRequest;
use App\Models\FiscalYear;
use App\Repositories\FiscalYearRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class FiscalYearController
 * @package App\Http\Controllers\API
 */

class FiscalYearAPIController extends AppBaseController
{
    /** @var  FiscalYearRepository */
    private $fiscalYearRepository;

    public function __construct(FiscalYearRepository $fiscalYearRepo)
    {
        $this->fiscalYearRepository = $fiscalYearRepo;
    }

    /**
     * Display a listing of the FiscalYear.
     * GET|HEAD /fiscalYears
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $fiscalYears = $this->fiscalYearRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($fiscalYears->toArray(), 'Fiscal Years retrieved successfully');
    }

    //Get Fiscal Year
    public function getActiveFiscalYear(Request $request)
    {
        $company_id = checkCompanyId($request);
        $fiscalYears = FiscalYear::where('status', 1)->where('company_id',$company_id)->get();
        if(empty($fiscalYears)) {
            $this->sendError('Fiscal year not found!');
        }

        return $this->sendResponse($fiscalYears, 'Fiscal years retrieve successfully');
    }

    // get fiscal years
    public function getFiscalYearList(Request $request)
    {
        $columns = ['label', 'start_date', 'end_date','company_id', 'status'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $company_id = checkCompanyId($request);

        $query = FiscalYear::with(['companies'])->orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('label', 'like', '%' .$searchValue. '%');
                $query->orWhere('start_date', 'like', '%' .$searchValue. '%');
                $query->orWhere('end_date', 'like', '%' .$searchValue. '%');
            });
        }

        $fiscal_years = $query->where('company_id',$company_id)->paginate($length);
        $return_data    = [
            'data' => $fiscal_years,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Fiscal years retrieved successfully');
    }

    /**
     * Store a newly created FiscalYear in storage.
     * POST /fiscalYears
     *
     * @param CreateFiscalYearAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateFiscalYearAPIRequest $request)
    {
        $this->validate($request, [
            'label' => 'required|unique:fiscal_years,label,NULL,id,company_id,'.$request->get('company_id'),
            'start_date'    => 'required',
            'end_date'    => 'required',
            'company_id'    => 'required',
            'status'    => 'required',
        ]);

        $start = date('Y-m-d', strtotime($request->get('start_date')));
        $end = date('Y-m-d', strtotime($request->get('end_date')));
        $company_id = checkCompanyId($request);

        $checkExists = FiscalYear::where(function ($query) use($start, $end){
                            return $query->where(function($query) use($start, $end){
                                return $query->where(function($query) use($start, $end){
                                    return $query->where('start_date', '<=', $start)
                                                ->where('end_date', '>=', $start);
                                })
                                ->orWhere(function($query) use($start, $end){
                                    return $query->where('end_date', '>=', $end)
                                                ->where('end_date', '<=', $end);
                                });
                            })
                            ->orWhere(function($query) use($start, $end){
                                return $query->where(function($query) use($start, $end){
                                    return $query->where('start_date', '>=', $start)
                                                ->where('start_date', '<=', $end);
                                });
                            })
                            ->orWhere(function($query) use($start, $end){
                                return $query->where(function($query) use($start, $end){
                                    return $query->where('end_date', '>=', $start)
                                                ->where('end_date', '<=', $end);
                                });
                            });
                    })->where('company_id',$company_id)->get();

        if(count($checkExists) > 0) {
            return $this->sendError("This fiscal year can't be added because this date range found in database!");
        }
        $input = $request->all();
        $input['company_id'] = 

        $status = $request->get('status');
        if($status == 1) {
            DB::beginTransaction();
            try{
                $fiscalYear = $this->fiscalYearRepository->create($input);
                $update_all = FiscalYear::where('id','!=', $fiscalYear->id)->update(['status' => 0]);
                DB::commit();
                return $this->sendResponse($fiscalYear->toArray(), 'FiscalYear Saved successfully');

            }catch(\Exception $e){
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            $fiscalYear = $this->fiscalYearRepository->create($input);
            return $this->sendResponse($fiscalYear->toArray(), 'FiscalYear saved successfully');
        }

    }

    /**
     * Display the specified FiscalYear.
     * GET|HEAD /fiscalYears/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var FiscalYear $fiscalYear */
        $fiscalYear = $this->fiscalYearRepository->find($id);

        if (empty($fiscalYear)) {
            return $this->sendError('Fiscal Year not found');
        }

        return $this->sendResponse($fiscalYear->toArray(), 'Fiscal Year retrieved successfully');
    }

    /**
     * Update the specified FiscalYear in storage.
     * PUT/PATCH /fiscalYears/{id}
     *
     * @param int $id
     * @param UpdateFiscalYearAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFiscalYearAPIRequest $request)
    {
        $this->validate($request, [
            'label' => 'required|unique:fiscal_years,label,'.$id.',id,company_id,'.$request->get('company_id'),
            'start_date'    => 'required',
            'end_date'    => 'required',
            'company_id'    => 'required',
            'status'    => 'required',
        ]);

        $start = date('Y-m-d', strtotime($request->get('start_date')));
        $end = date('Y-m-d', strtotime($request->get('end_date')));
        $company_id = checkCompanyId($request);

        $checkExists = FiscalYear::where('id', '!=', $id)->where(function ($query) use($start, $end){
            return $query->where(function($query) use($start, $end){
                return $query->where(function($query) use($start, $end){
                    return $query->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $start);
                })
                    ->orWhere(function($query) use($start, $end){
                        return $query->where('end_date', '>=', $end)
                            ->where('end_date', '<=', $end);
                    });
            })
            ->orWhere(function($query) use($start, $end){
                return $query->where(function($query) use($start, $end){
                    return $query->where('start_date', '>=', $start)
                        ->where('start_date', '<=', $end);
                });
            })
            ->orWhere(function($query) use($start, $end){
                return $query->where(function($query) use($start, $end){
                    return $query->where('end_date', '>=', $start)
                        ->where('end_date', '<=', $end);
                });
            });
        })->where('company_id',$company_id)->get();

        if(count($checkExists) > 0) {
            return $this->sendError("This fiscal year can't be updated because this date range found in database!");
        }

        $input = $request->all();
        $input['company_id'] = checkCompanyId($request);

        /** @var FiscalYear $fiscalYear */
        $fiscalYear = $this->fiscalYearRepository->find($id);

        if (empty($fiscalYear)) {
            return $this->sendError('Fiscal Year not found');
        }

        $status = $request->get('status');
        if($status == 1) {
            DB::beginTransaction();
            try{
                $fiscalYear = $this->fiscalYearRepository->update($input, $id);
                $update_all = FiscalYear::where('id','!=', $id)->update(['status' => 0]);
                DB::commit();
                return $this->sendResponse($fiscalYear->toArray(), 'FiscalYear updated successfully');

            }catch(\Exception $e){
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            $fiscalYear = $this->fiscalYearRepository->update($input, $id);
            return $this->sendResponse($fiscalYear->toArray(), 'FiscalYear updated successfully');
        }


    }


    // Update Only Status
    public function updateStatus(Request $request)
    {
        $fiscal_year = FiscalYear::find($request->get('item_id'));

        if(empty($fiscal_year)) {
            return $this->sendError('Data do not found!');
        }

        $status = $request->get('status');
        if($status == 1) {
            DB::beginTransaction();
            try{
                $update_data = $fiscal_year->update(['status' => $status]);
                $update_all = FiscalYear::where('id', '!=', $fiscal_year->id)->update(['status' => 0]);
                DB::commit();

                return $this->sendSuccess('Fiscal year update successfully!');
            }catch(\Exception $e){
                DB::rollBack();
                return $this->sendError($e->getMessage());
            }
        }else{
            $update_data = $fiscal_year->update(['status' => $status]);

            return $this->sendSuccess('Fiscal year update successfully!');
        }
    }

    /**
     * Remove the specified FiscalYear from storage.
     * DELETE /fiscalYears/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var FiscalYear $fiscalYear */
        $fiscalYear = $this->fiscalYearRepository->find($id);

        if (empty($fiscalYear)) {
            return $this->sendError('Fiscal Year not found');
        }

        $fiscalYear->delete();

        return $this->sendSuccess('Fiscal Year deleted successfully');
    }
}
