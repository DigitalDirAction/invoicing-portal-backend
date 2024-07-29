<?php

namespace App\Repositories;

use App\Interfaces\PaymentsRepositoryInterface;
use App\Models\Payments;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentsRepository implements PaymentsRepositoryInterface
{
    public function getAllPayments($id)
    {
        return Payments::where('invoice_id', $id)
            ->paginate(10);
    }
    public function createPayment(array $paymentDetails)
    {
        return Payments::create($paymentDetails);
    }
    public function getPaymentById($PaymentID)
    {
        return Payments::where('id', $PaymentID)->get();
    }
    public function updatePayment($PaymentID, array $paymentDetails)
    {
        if (isset($PaymentID)) {
            $payment = Payments::find($PaymentID);
            if ($payment && $paymentDetails['receipt']) {
                if ($payment->receipt) {
                    Storage::disk('public')->delete($payment->receipt);
                }
            }
        }
        return Payments::whereId($PaymentID)->update($paymentDetails);
    }
    public function sumOfTotalReceivedAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null)
    {
        $query = Payments::query()
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id');
        if ($from_date) {
            $query->whereDate('payments.payment_date', '>=', $from_date);
        }
        if ($to_date) {
            $query->whereDate('payments.payment_date', '<=', $to_date);
        }
        if ($createdBy) {
            $query->where('invoices.created_by', $createdBy);
        }

        return $query->sum('payments.amount_received');
    }

    public function deletePayment($PaymentID)
    {
        try {
            $invoice = Payments::findOrFail($PaymentID);
            $invoice->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
