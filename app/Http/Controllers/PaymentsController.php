<?php

namespace App\Http\Controllers;

use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\PaymentsRepositoryInterface;
use F9Web\ApiResponseHelpers;

class PaymentsController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private PaymentsRepositoryInterface $paymentsRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $invoiceID): JsonResponse
    {
        try {

            $user = $this->paymentsRepository->getAllPayments($invoiceID);

            $reponse = getResponse($user, '', "Payments List", 201);
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

            $userID = Auth::id();
            $receipt = '';

            $paymentDetails = $request->validate([
                'invoice_id' => 'required',
                'invoice_number' => 'required',
                'customer_name' => 'required',
                'payment_number' => 'required',
                'amount_received' => 'required',
                'amount_due' => 'required',
                'payment_date' => 'required',
                'payment_method' => 'required',
                'reference' => 'required',
                'receipt' => '',
            ]);

            if ($request->hasFile('receipt')) {

                $file = $request->file('receipt');
                if ($file) {
                    $imageName = $paymentDetails['invoice_number'] . '-' . $file->getClientOriginalName();
                    $path = $file->storeAs('invoice_receipts/' . $paymentDetails['invoice_number'], $imageName, 'public');
                    $receipt = $path;
                }
            }

            $paymentDetails['created_by'] = $userID;
            $paymentDetails['updated_by'] = $userID;

            $newpaymentDetails = array_merge($paymentDetails, ['receipt' => $receipt]);
            $payment = $this->paymentsRepository->createPayment($newpaymentDetails);

            $response = getResponse($payment, '', "Payment added successfully", 201);
            return $this->respondWithSuccess($response);
        } catch (\Exception $e) {
            $response = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($response);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $paymentID): JsonResponse
    {
        try {

            $user = $this->paymentsRepository->getPaymentById($paymentID);

            $reponse = getResponse($user, '', "Payment Data", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $paymentID): JsonResponse
    {
        try {

            $userID = Auth::id();
            $receipt = '';

            $paymentDetails = $request->validate([
                'invoice_number' => 'required',
                'customer_name' => 'required',
                'payment_number' => 'required',
                'amount_received' => 'required',
                'amount_due' => 'required',
                'payment_date' => 'required',
                'payment_method' => 'required',
                'reference' => 'required',
                'receipt' => '',
            ]);

            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                if ($file) {
                    $imageName = $paymentDetails['invoice_number'] . '-' . $file->getClientOriginalName();
                    $path = $file->storeAs('invoice_receipts/' . $paymentDetails['invoice_number'], $imageName, 'public');
                    $receipt = $path;
                }
            }

            $paymentDetails['updated_by'] = $userID;

            $newpaymentDetails = array_merge($paymentDetails, ['receipt' => $receipt]);

            $payment = $this->paymentsRepository->updatePayment($paymentID, $newpaymentDetails);

            $response = getResponse($payment, '', "Payment added successfully", 201);
            return $this->respondWithSuccess($response);
        } catch (\Exception $e) {
            $response = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $paymentID): JsonResponse
    {
        try {

            $user = $this->paymentsRepository->deletePayment($paymentID);

            if ($user) {
                $reponse = getResponse($user, '', "Payment Deleted Successfully", 201);

            } else {
                $reponse = getResponse($user, '', "Payment Not Found", 404);
            }
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($reponse);
        }
    }
}


