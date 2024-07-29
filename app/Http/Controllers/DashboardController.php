<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\PaymentsRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Support\Facades\Log;
use F9Web\ApiResponseHelpers;

class DashboardController extends Controller
{
    use ApiResponseHelpers;
    public function __construct(private PaymentsRepositoryInterface $paymentsRepository, private InvoiceRepositoryInterface $invoiceRepository)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            dd(Auth::check());
            $userID = Auth::id();
            $from_date = $request->query('from_date');
            $to_date = $request->query('to_date');
            $calculateUnPaid = '';
            $invoicesTotalAmount['total_amount'] = $this->invoiceRepository->sumOfTotalAmountOfInvoices($from_date, $to_date, $userID);
            $invoicesTotalAmount['received'] = $this->paymentsRepository->sumOfTotalReceivedAmountOfInvoices($from_date, $to_date, $userID);
            if ($invoicesTotalAmount['total_amount']) {
                $calculateUnPaid = $invoicesTotalAmount['total_amount'] - $invoicesTotalAmount['received'];
            }
            $invoicesTotalAmount['unpaid'] = $calculateUnPaid;
            $invoicesTotalAmount['over_due'] = $this->invoiceRepository->sumOfOverdueAmountOfInvoices($from_date, $to_date, $userID);
            $reponse = getResponse($invoicesTotalAmount, '', "Dashboard Data", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function getRecentInvoices(Request $request): JsonResponse
    {
        try {
            $userID = Auth::id();
            $recentInvoices = $this->invoiceRepository->getRecentInvoices($userID);
            $reponse = getResponse($recentInvoices, '', "Dashboard Data", 200);
            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}




