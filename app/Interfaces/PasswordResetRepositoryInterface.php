<?php

namespace App\Interfaces;

interface PasswordResetRepositoryInterface
{
    public function createToken(array $userDetails);
    public function deleteToken($email);
    public function verifyToken($token);
    public function updateIsVerifies($token, array $newDetails);
    public function deleteFiveMinuteOldToken($formatted);
}