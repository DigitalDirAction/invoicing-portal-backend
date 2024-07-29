<?php

namespace App\Interfaces;

interface BankingDetailRepositoryInterface
{
    public function getAllBanks($userID);
    public function getBankId($BankID);
    public function deleteBank($BankID);
    public function createBank(array $bankDetails);
    public function updateBank($BankID, array $bankDetails);
}