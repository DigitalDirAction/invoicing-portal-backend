<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Invoices\storeInvoiceRequest;
use App\Http\Requests\Invoices\UpdateInvoiceRequest;
use App\Interfaces\InvoiceRepositoryInterface;
use F9Web\ApiResponseHelpers;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private InvoiceRepositoryInterface $invoiceRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $userID = Auth::id();
            $user = $this->invoiceRepository->getAllInvoices($userID);

            $reponse = getResponse($user, '', "Invoices List", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(storeInvoiceRequest $request): JsonResponse
    {
        try {
            $userDetails = $request->validated();
            // Generate a random invoice number
            $randomInvoiceNumber = mt_rand(100000, 999999);
            $invoiceNumber = 'INV-' . $randomInvoiceNumber;
            $invoiceService = [];
            // Get the authenticated user ID
            $userID = Auth::id();

            // Validate the main invoice details
            $invoiceDetails = $request->setInvoiceData();

            // Validate the service details
            $serviceDetails = $request->setInvoiceServiceData();

            // Assign the generated invoice number and created_by fields
            $invoiceDetails['invoice_number'] = $invoiceNumber;
            $invoiceDetails['created_by'] = $userID;

            // Create the invoice using the repository
            $invoice = $this->invoiceRepository->createInvoice($invoiceDetails);

            for ($i = 0; $i < count($serviceDetails['service_description']); $i++) {
                $invoiceService = [
                    'invoice_id' => $invoice->id,
                    'service_description' => $serviceDetails['service_description'][$i],
                    'quantity' => $serviceDetails['quantity'][$i],
                    'rate' => $serviceDetails['rate'][$i],
                    'tax' => $serviceDetails['tax'][$i],
                    'amount' => $serviceDetails['amount'][$i]
                ];
                $this->invoiceRepository->createInvoiceService($invoiceService);
            }

            // Prepare the response
            $response = getResponse('', '', "Invoice added successfully", 201);
            return $this->respondWithSuccess($response);
        } catch (\Exception $e) {
            // Handle exceptions and prepare the error response
            $response = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($response);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $InvoiceID): JsonResponse
    {
        try {

            $user = $this->invoiceRepository->getInvoiceById($InvoiceID);

            $reponse = getResponse($user, '', "Invoice Data", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, $InvoiceID): JsonResponse
    {
        try {


            $invoiceService = [];

            $invoiceDetails = $request->setInvoiceData();

            $serviceDetails = $request->setInvoiceServiceData();
            $this->invoiceRepository->updateInvoice($InvoiceID, $invoiceDetails);
            $this->invoiceRepository->deleteInvoiceService($InvoiceID);
            for ($i = 0; $i < count($serviceDetails['service_description']); $i++) {
                $invoiceService = [
                    'invoice_id' => $InvoiceID,
                    'service_description' => $serviceDetails['service_description'][$i],
                    'quantity' => $serviceDetails['quantity'][$i],
                    'rate' => $serviceDetails['rate'][$i],
                    'tax' => $serviceDetails['tax'][$i],
                    'amount' => $serviceDetails['amount'][$i]
                ];
                $this->invoiceRepository->createInvoiceService($invoiceService);
            }

            $response = getResponse('', '', "Invoice Updated successfully", 201);
            return $this->respondWithSuccess($response);
        } catch (\Exception $e) {
            $response = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $InvoiceID): JsonResponse
    {
        try {

            $user = $this->invoiceRepository->deleteInvoice($InvoiceID);

            if ($user) {
                $reponse = getResponse($user, '', "Invoice Deleted Successfully", 200);

            } else {
                $reponse = getResponse($user, '', "Invoice Not Found", 404);
            }
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }
}

