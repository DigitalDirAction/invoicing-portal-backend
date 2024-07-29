<?php

namespace App\Interfaces;

interface InvoiceRepositoryInterface
{
    public function getAllInvoices($userID);
    public function getInvoiceById($InvoiceID);
    public function deleteInvoice($InvoiceID);
    public function createInvoice(array $invoiceDetails);
    public function updateInvoice($InvoiceID, array $invoiceDetails);
    public function sumOfTotalAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null);
    public function sumOfOverdueAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null);
    public function createInvoiceService(array $invoiceService);
    public function deleteInvoiceService($InvoiceID);
}