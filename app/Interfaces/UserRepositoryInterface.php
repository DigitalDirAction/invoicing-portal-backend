<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getUserById($UserID);
    public function deleteUser($UserID);
    public function createUser(array $userDetails, $roleId);
    public function updateUser($UserID, array $newDetails, $roleId);
    public function findUserByEmail($email);
    public function logoutUser();
    public function generateTwoFactorCode($UserID);
    public function deleteTenMinuteOldAuthCode($formatted);
    public function verifyAuthCode($code);
    public function resetTwoFactorCode($UserID);

    public function updateUserProfile($profileImage, array $userDetails);
}