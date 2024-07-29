<?php

namespace App\Interfaces;

interface PaymentsRepositoryInterface
{
    public function getAllPayments($id);
    public function createPayment(array $paymentDetails);
    public function getPaymentById($PaymentID);
    public function updatePayment($PaymentID, array $paymentDetails);
    public function sumOfTotalReceivedAmountOfInvoices($from_date = null, $to_date = null, $createdBy = null);
    public function deletePayment($PaymentID);
}