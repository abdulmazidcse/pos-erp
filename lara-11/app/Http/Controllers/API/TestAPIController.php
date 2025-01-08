<?php

namespace App\Http\Controllers\API;
 
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class TestController
 * @package App\Http\Controllers\API
 */

class TestAPIController extends AppBaseController
{ 
    public function index(Request $request)
    { 
        return $this->sendResponse(array(), 'Tests retrieved successfully');
    }

    /**
     * Store a newly created Test in storage.
     * POST /tests
     *
     * @param CreateTestAPIRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        if($request['files_base']){ 
            $file = $request['files_base'];
            $filename = $file->getClientOriginalName(); 
            $extension = $file->getClientOriginalExtension();
            $path = public_path().'/uploads/videos/';
            $fileNameToStore = time().'.'.$extension;
            $file->move($path, $fileNameToStore);  
        } 
        // $input = $request->all();

        // $test = $this->testRepository->create($input);

        return $this->sendResponse(array(), 'Test saved successfully');
    }

    /**
     * Display the specified Test.
     * GET|HEAD /tests/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Test $test */
        $test = $this->testRepository->find($id);

        if (empty($test)) {
            return $this->sendError('Test not found');
        }

        return $this->sendResponse($test->toArray(), 'Test retrieved successfully');
    }

    /**
     * Update the specified Test in storage.
     * PUT/PATCH /tests/{id}
     *
     * @param int $id
     * @param UpdateTestAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTestAPIRequest $request)
    {
        $input = $request->all();

        /** @var Test $test */
        $test = $this->testRepository->find($id);

        if (empty($test)) {
            return $this->sendError('Test not found');
        }

        $test = $this->testRepository->update($input, $id);

        return $this->sendResponse($test->toArray(), 'Test updated successfully');
    }

    /**
     * Remove the specified Test from storage.
     * DELETE /tests/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Test $test */
        $test = $this->testRepository->find($id);

        if (empty($test)) {
            return $this->sendError('Test not found');
        }

        $test->delete();

        return $this->sendSuccess('Test deleted successfully');
    }

    public function fileUpload(Request $request){

    }
}
