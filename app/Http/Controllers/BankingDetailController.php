<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BankingDetail\AddBankRequest;
use App\Http\Requests\BankingDetail\UpdateBankRequest;
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

            $reponse = getResponse($user, '', "Banks List", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddBankRequest $request): JsonResponse
    {
        try {

            $data = $request->validated();

            $user = $this->bankingDetailRepository->createBank($data);

            $reponse = getResponse($user, '', "Bank Add Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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

            $reponse = getResponse($user, '', "Bank Data", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBankRequest $request, $bankID): JsonResponse
    {
        try {

            $userDetails = $request->validated();

            $user = $this->bankingDetailRepository->updateBank($bankID, $userDetails);

            $reponse = getResponse($user, '', "Bank Updated Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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
                $reponse = getResponse($user, '', "Bank Deleted Successfully", 200);

            } else {
                $reponse = getResponse($user, '', "Bank Not Found", 404);
            }
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }
}
