<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\addCustomerRequest;
use App\Http\Requests\UpdateUserRequest;
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

            $user = $this->customerRepository->getAllCustomers();

            $reponse = getResponse($user, '', "Customers List", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', $e->getMessage(), 404);
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
                'customer_type' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => 'required',
                'currency' => 'required',
                'email' => 'email|unique:customers,email',
                'phone_number' => '',
                'mobile_number' => 'required',
                'address' => 'required',
                'logo' => '',
            ]);

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
            $reponse = getResponse('', '', $e->getMessage(), 404);
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

            $reponse = getResponse($user, '', "Customer Data", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $userDetails = $request->validate([
                'customer_id' => 'required',
                'customer_type' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => 'required',
                'currency' => 'required',
                'email' => 'email|unique:customers,email,' . $request->customer_id,
                'phone_number' => '',
                'mobile_number' => 'required',
                'address' => 'required',
                'logo' => '',
            ]);
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
            $reponse = getResponse('', '', $e->getMessage(), 404);
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

            $reponse = getResponse($user, '', "Customer Deleted Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($reponse);
        }
    }
}