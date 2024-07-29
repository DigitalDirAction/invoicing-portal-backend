<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Models\Invoice;
use App\Models\InvoiceService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function getAllInvoices($userID)
    {
        return Invoice::with('invoiceServices', 'bankingDetail')
            ->where('created_by', $userID)
            ->paginate(10);
    }
    public function createInvoice(array $invoiceDetails)
    {
        return Invoice::create($invoiceDetails);
    }
    public function getInvoiceById($InvoiceID)
    {
        return Invoice::with('invoiceServices', 'bankingDetail', 'customerDetail')
            ->where('id', $InvoiceID)
            ->get();
    }
    public function updateInvoice($InvoiceID, array $invoiceDetails)
    {
        return Invoice::whereId($InvoiceID)->update($invoiceDetails);
    }
    public function sumOfTotalAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null)
    {
        $invoices = Invoice::where(['created_by' => $createdBy])
            ->whereBetween('invoice_date', [$from_date, $to_date]) // Date range filter
            ->get();
        return $invoices->sum('total_amount');
    }
    public function sumOfOverdueAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null)
    {
        $today = Carbon::today();
        $query = DB::table('invoices')
            ->leftJoin('payments', 'invoices.id', '=', 'payments.invoice_id')
            ->select('invoices.id', 'invoices.total_amount', DB::raw('SUM(payments.amount_received) as amount_of_invoice_id'))
            ->where('invoices.created_by', $createdBy)
            ->where('invoices.due_date', '<', $today)
            ->groupBy('invoices.id')
            ->havingRaw('invoices.total_amount > IFNULL(SUM(payments.amount_received), 0)');

        if ($from_date && $to_date) {
            $query->whereBetween('invoices.invoice_date', [$from_date, $to_date]);
        }

        $overduePaymentsSum = $query->get()
            ->sum(function ($invoice) {
                return $invoice->total_amount - $invoice->amount_of_invoice_id;
            });

        return $overduePaymentsSum;
    }
    public function deleteInvoice($InvoiceID)
    {
        try {
            $invoice = Invoice::findOrFail($InvoiceID);
            $service = InvoiceService::where('invoice_id', $InvoiceID)->get();

            if ($service) {
                foreach ($service as $record) {
                    $record->delete();
                }
            }
            $invoice->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
    public function createInvoiceService(array $invoiceService)
    {
        return InvoiceService::create($invoiceService);
    }
    public function deleteInvoiceService($InvoiceID)
    {
        $invoice = InvoiceService::where('invoice_id', $InvoiceID)->get();
        if ($invoice) {
            foreach ($invoice as $record) {
                $record->delete();
            }
        }
    }
}
