<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateEntryTypeAPIRequest;
use App\Http\Requests\API\UpdateEntryTypeAPIRequest;
use App\Models\EntryType;
use App\Repositories\EntryTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class EntryTypeController
 * @package App\Http\Controllers\API
 */

class EntryTypeAPIController extends AppBaseController
{
    /** @var  EntryTypeRepository */
    private $entryTypeRepository;

    public function __construct(EntryTypeRepository $entryTypeRepo)
    {
        $this->entryTypeRepository = $entryTypeRepo;
    }

    /**
     * Display a listing of the EntryType.
     * GET|HEAD /entryTypes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $entryTypes = $this->entryTypeRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($entryTypes->toArray(), 'Entry Types retrieved successfully');
    }

    public function getEntryTypeList(Request $request)
    {
        $columns = ['label', 'name', 'description', 'numbering', 'prefix', 'suffix', 'zero_padding', 'restrictions'];

        $length = $request->input('length');
        $column = $request->input('column');
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $query = EntryType::orderBy($columns[$column], $dir);

        if($searchValue) {
            $query->where(function ($query) use ($searchValue) {
                $query->where('label', 'like', '%' .$searchValue. '%');
                $query->orWhere('name', 'like', '%' .$searchValue. '%');
                $query->orWhere('numbering', 'like', '%' .$searchValue. '%');
            });
        }

        $entry_types = $query->paginate($length);
        $return_data    = [
            'data' => $entry_types,
            'draw' => $request->input('draw')
        ];
        return $this->sendResponse($return_data, 'Entry Types retrieved successfully');
    }

    /**
     * Store a newly created EntryType in storage.
     * POST /entryTypes
     *
     * @param CreateEntryTypeAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateEntryTypeAPIRequest $request)
    {
        $this->validate($request, [
            'label'     => 'required',
            'name'      => 'required|unique:entry_types,name'
        ]);
        $input = $request->all();

        $entryType = $this->entryTypeRepository->create($input);

        return $this->sendResponse($entryType->toArray(), 'Entry Type saved successfully');
    }

    /**
     * Display the specified EntryType.
     * GET|HEAD /entryTypes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var EntryType $entryType */
        $entryType = $this->entryTypeRepository->find($id);

        if (empty($entryType)) {
            return $this->sendError('Entry Type not found');
        }

        return $this->sendResponse($entryType->toArray(), 'Entry Type retrieved successfully');
    }

    /**
     * Update the specified EntryType in storage.
     * PUT/PATCH /entryTypes/{id}
     *
     * @param int $id
     * @param UpdateEntryTypeAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateEntryTypeAPIRequest $request)
    {
        $this->validate($request, [
            'label'     => 'required',
            'name'      => 'required|unique:entry_types,name,'.$id
        ]);
        $input = $request->all();

        /** @var EntryType $entryType */
        $entryType = $this->entryTypeRepository->find($id);

        if (empty($entryType)) {
            return $this->sendError('Entry Type not found');
        }

        $entryType = $this->entryTypeRepository->update($input, $id);

        return $this->sendResponse($entryType->toArray(), 'EntryType updated successfully');
    }

    /**
     * Remove the specified EntryType from storage.
     * DELETE /entryTypes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var EntryType $entryType */
        $entryType = $this->entryTypeRepository->find($id);

        if (empty($entryType)) {
            return $this->sendError('Entry Type not found');
        }

        $entryType->delete();

        return $this->sendSuccess('Entry Type deleted successfully');
    }
}
