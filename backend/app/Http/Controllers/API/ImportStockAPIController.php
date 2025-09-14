<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateImportStockAPIRequest;
use App\Http\Requests\API\UpdateImportStockAPIRequest;
use App\Models\ImportStock;
use App\Repositories\ImportStockRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class ImportStockAPIController
 */
class ImportStockAPIController extends AppBaseController
{
    private ImportStockRepository $importStockRepository;

    public function __construct(ImportStockRepository $importStockRepo)
    {
        $this->importStockRepository = $importStockRepo;
    }

    /**
     * Display a listing of the ImportStocks.
     * GET|HEAD /import-stocks
     */
    public function index(Request $request): JsonResponse
    {
        $importStocks = $this->importStockRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($importStocks->toArray(), 'Import Stocks retrieved successfully');
    }

    /**
     * Store a newly created ImportStock in storage.
     * POST /import-stocks
     */
    public function store(CreateImportStockAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $importStock = $this->importStockRepository->create($input);

        return $this->sendResponse($importStock->toArray(), 'Import Stock saved successfully');
    }

    /**
     * Display the specified ImportStock.
     * GET|HEAD /import-stocks/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var ImportStock $importStock */
        $importStock = $this->importStockRepository->find($id);

        if (empty($importStock)) {
            return $this->sendError('Import Stock not found');
        }

        return $this->sendResponse($importStock->toArray(), 'Import Stock retrieved successfully');
    }

    /**
     * Update the specified ImportStock in storage.
     * PUT/PATCH /import-stocks/{id}
     */
    public function update($id, UpdateImportStockAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var ImportStock $importStock */
        $importStock = $this->importStockRepository->find($id);

        if (empty($importStock)) {
            return $this->sendError('Import Stock not found');
        }

        $importStock = $this->importStockRepository->update($input, $id);

        return $this->sendResponse($importStock->toArray(), 'ImportStock updated successfully');
    }

    /**
     * Remove the specified ImportStock from storage.
     * DELETE /import-stocks/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var ImportStock $importStock */
        $importStock = $this->importStockRepository->find($id);

        if (empty($importStock)) {
            return $this->sendError('Import Stock not found');
        }

        $importStock->delete();

        return $this->sendSuccess('Import Stock deleted successfully');
    }
}
