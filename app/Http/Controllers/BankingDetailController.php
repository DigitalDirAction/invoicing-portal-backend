<?php

namespace App\Http\Controllers;

use App\Models\BankingDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\BankingDetailRepositoryInterface;
use F9Web\ApiResponseHelpers;

class BankingDetailController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private BankingDetailRepositoryInterface $bankingDetailRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $userID = Auth::id();
            $user = $this->bankingDetailRepository->getAllBanks($userID);

            $reponse = getResponse($user, '', "Banks List", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $userDetails = $request->validate([
                'user_id' => 'required',
                'bank_name' => 'required',
                'branch_code' => 'required',
                'account_title' => 'required',
                'iban_number' => 'required|unique:banking_details,iban_number',
            ]);

            $user = $this->bankingDetailRepository->createBank($userDetails);

            $reponse = getResponse($user, '', "Bank Add Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $bankID): JsonResponse
    {
        try {

            $user = $this->bankingDetailRepository->getBankId($bankID);

            $reponse = getResponse($user, '', "Bank Data", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $bankID): JsonResponse
    {
        try {

            $userDetails = $request->validate([
                'bank_name' => 'required',
                'branch_code' => 'required',
                'account_title' => 'required',
                'iban_number' => 'required|unique:banking_details,iban_number,' . $bankID,

            ]);

            $user = $this->bankingDetailRepository->updateBank($bankID, $userDetails);

            $reponse = getResponse($user, '', "Bank Updated Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $bankID): JsonResponse
    {
        try {

            $user = $this->bankingDetailRepository->deleteBank($bankID);

            if ($user) {
                $reponse = getResponse($user, '', "Bank Deleted Successfully", 201);

            } else {
                $reponse = getResponse($user, '', "Bank Not Found", 404);
            }
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }
}
