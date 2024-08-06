<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\addCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Interfaces\CustomerRepositoryInterface;
use F9Web\ApiResponseHelpers;

class CustomerController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private CustomerRepositoryInterface $customerRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $createdBy = Auth::id();
            $user = $this->customerRepository->getAllCustomers($createdBy);

            $reponse = getResponse($user, '', "Customers List", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(addCustomerRequest $request): JsonResponse
    {
        $userID = Auth::id();
        try {

            $userDetails = $request->validated();
            $userDetails['created_by'] = $userID;
            $userDetails['updated_by'] = $userID;

            $logo = '';

            if ($request->hasFile('logo')) {

                $file = $request->file('logo');
                if ($file) {
                    $imageName = $userDetails['email'] . '-' . $file->getClientOriginalName();
                    $path = $file->storeAs('customer_profile/logo', $imageName, 'public');
                    $logo = $path;
                }
            }

            $user = $this->customerRepository->createCustomer($userDetails, $logo);

            $reponse = getResponse($user, '', "Customer Add Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $customerID): JsonResponse
    {
        try {

            $user = $this->customerRepository->getCustomerById($customerID);

            $reponse = getResponse($user, '', "Customer Data", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request): JsonResponse
    {
        $userID = Auth::id();
        try {

            $userDetails = $request->validated();
            $userDetails['updated_by'] = $userID;
            $logo = '';

            if ($request->hasFile('logo')) {

                $file = $request->file('logo');
                if ($file) {
                    $imageName = $userDetails['email'] . '-' . $file->getClientOriginalName();
                    $path = $file->storeAs('customer_profile/logo', $imageName, 'public');
                    $logo = $path;
                }
            }
            $user = $this->customerRepository->updateCustomer($logo, $userDetails);

            $reponse = getResponse($user, '', "Customer Updated Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $customerID)
    {
        try {

            $user = $this->customerRepository->deleteCustomer($customerID);

            $reponse = getResponse($user, '', "Customer Deleted Successfully", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }
}
