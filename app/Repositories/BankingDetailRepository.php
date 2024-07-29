<?php

namespace App\Repositories;

use App\Interfaces\BankingDetailRepositoryInterface;
use App\Models\BankingDetail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BankingDetailRepository implements BankingDetailRepositoryInterface
{
    public function getAllBanks($userID)
    {
        return BankingDetail::where('user_id', $userID)->get();
    }

    public function getBankId($BankID)
    {
        return BankingDetail::findOrFail($BankID);
    }

    public function deleteBank($BankID)
    {
        try {
            $bank = BankingDetail::findOrFail($BankID);
            $bank->delete();
            return true;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function createBank(array $bankDetails)
    {
        $bank = BankingDetail::create($bankDetails);

        return $bank;
    }
    public function updateBank($BankID, array $bankDetails)
    {
        $bank = BankingDetail::whereId($BankID)->update($bankDetails);

        return $bank;
    }


}
