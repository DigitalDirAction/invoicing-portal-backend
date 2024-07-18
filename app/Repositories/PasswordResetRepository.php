<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Password;
use App\Interfaces\PasswordResetRepositoryInterface;
use App\Models\User;
use App\Models\PasswordReset;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function createToken(array $userDetails)
    {
        return PasswordReset::create($userDetails);
    } 

    public function deleteFiveMinuteOldToken($formatted)
    {
        return PasswordReset::where('created_at', '<=', $formatted)->delete();
    }

    public function verifyToken($token){
        return PasswordReset::where('token', $token)->first();
    }

    public function updateIsVerifies($token, array $newDetails)
    {
        return PasswordReset::where('token', $token)->update($newDetails);
    }

    public function deleteToken($email){
        return PasswordReset::where('email', $email)->delete();
    }
    
    
}