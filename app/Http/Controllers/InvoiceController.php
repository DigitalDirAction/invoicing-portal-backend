<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Invoices\storeInvoiceRequest;
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

            $reponse = getResponse($user, '', "Invoices List", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }
    private function validateInvoiceItems($request)
    {
        return $request->validate([
            'service_description' => 'sometimes|array',
            'quantity' => 'sometimes|array',
            'rate' => 'sometimes|array',
            'tax' => 'sometimes|array',
            'amount' => 'sometimes|array'
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Generate a random invoice number
            $randomInvoiceNumber = mt_rand(100000, 999999);
            $invoiceNumber = 'INV-' . $randomInvoiceNumber;
            $invoiceService = [];
            // Get the authenticated user ID
            $userID = Auth::id();

            // Validate the main invoice details
            $invoiceDetails = $request->validate([
                'customer_id' => 'required',
                'bank_id' => 'required',
                'currency' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'quantity_text' => 'required',
                'rate_text' => 'required',
                'tax_text' => 'required',
                'amount_text' => 'required',
                'sub_total' => 'required',
                'total_amount' => 'required',
                'customer_note' => 'required',
                'status' => 'required',
            ]);

            // Validate the service details
            $serviceDetails = $this->validateInvoiceItems($request);

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
            $response = getResponse('', '', '', 404);
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

            $reponse = getResponse($user, '', "Invoice Data", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $InvoiceID): JsonResponse
    {
        try {


            $invoiceService = [];

            $invoiceDetails = $request->validate([
                'customer_id' => 'required',
                'bank_id' => 'required',
                'currency' => 'required',
                'invoice_date' => 'required',
                'due_date' => 'required',
                'quantity_text' => 'required',
                'rate_text' => 'required',
                'tax_text' => 'required',
                'amount_text' => 'required',
                'sub_total' => 'required',
                'total_amount' => 'required',
                'customer_note' => 'required',
                'status' => 'required',
            ]);

            $serviceDetails = $this->validateInvoiceItems($request);
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
            $response = getResponse('', '', '', 404);
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
                $reponse = getResponse($user, '', "Invoice Deleted Successfully", 201);

            } else {
                $reponse = getResponse($user, '', "Invoice Not Found", 404);
            }
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }
}

