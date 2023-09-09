<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMobileWalletAPIRequest;
use App\Http\Requests\API\UpdateMobileWalletAPIRequest;
use App\Models\MobileWallet;
use App\Repositories\MobileWalletRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class MobileWalletController
 * @package App\Http\Controllers\API
 */

class MobileWalletAPIController extends AppBaseController
{
    /** @var  MobileWalletRepository */
    private $mobileWalletRepository;

    public function __construct(MobileWalletRepository $mobileWalletRepo)
    {
        $this->mobileWalletRepository = $mobileWalletRepo;
    }

    /**
     * Display a listing of the MobileWallet.
     * GET|HEAD /mobileWallets
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $mobileWallets = $this->mobileWalletRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($mobileWallets->toArray(), 'Mobile Wallets retrieved successfully');
    }

    /**
     * Store a newly created MobileWallet in storage.
     * POST /mobileWallets
     *
     * @param CreateMobileWalletAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateMobileWalletAPIRequest $request)
    {
        $this->validate($request, [
            'mobile_wallet' => 'required',
            'agent_name' => 'required',
            'mobile_number' => 'required',
            'company_id' => 'required', 
        ]);
        $input = $request->all();

        $mobileWallet = $this->mobileWalletRepository->create($input);

        return $this->sendResponse($mobileWallet->toArray(), 'Mobile Wallet saved successfully');
    }

    /**
     * Display the specified MobileWallet.
     * GET|HEAD /mobileWallets/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var MobileWallet $mobileWallet */
        $mobileWallet = $this->mobileWalletRepository->find($id);

        if (empty($mobileWallet)) {
            return $this->sendError('Mobile Wallet not found');
        }

        return $this->sendResponse($mobileWallet->toArray(), 'Mobile Wallet retrieved successfully');
    }

    /**
     * Update the specified MobileWallet in storage.
     * PUT/PATCH /mobileWallets/{id}
     *
     * @param int $id
     * @param UpdateMobileWalletAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMobileWalletAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileWallet $mobileWallet */
        $mobileWallet = $this->mobileWalletRepository->find($id);

        if (empty($mobileWallet)) {
            return $this->sendError('Mobile Wallet not found');
        }

        $mobileWallet = $this->mobileWalletRepository->update($input, $id);

        return $this->sendResponse($mobileWallet->toArray(), 'Mobile Wallet updated successfully');
    }

    /**
     * Remove the specified MobileWallet from storage.
     * DELETE /mobileWallets/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var MobileWallet $mobileWallet */
        $mobileWallet = $this->mobileWalletRepository->find($id);

        if (empty($mobileWallet)) {
            return $this->sendError('Mobile Wallet not found');
        }

        $mobileWallet->delete();

        return $this->sendSuccess('Mobile Wallet deleted successfully');
    }
}
