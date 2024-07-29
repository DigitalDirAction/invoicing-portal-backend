<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Password;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use InvalidArgumentException;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($UserID)
    {
        return User::findOrFail($UserID);
    }

    public function deleteUser($UserID)
    {
        $deleteUser = User::findOrFail($UserID);
        if ($deleteUser) {
            $deleteUser->delete();
        }
    }

    public function createUser(array $userDetails, $roleId)
    {
        $user = User::create($userDetails);

        switch ($roleId) {
            case 1:
                $user->assignRole('Admin');
                break;
            default:
                throw new InvalidArgumentException("Invalid role ID: $roleId");
        }

        $user->load([
            'roles' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        $user->roles->makeHidden('pivot');

        return $user;
    }

    public function updateUserProfile($profileImage, array $userDetails)
    {
        $user = User::where('id', $userDetails['user_id'])->first();

        if ($user) {
            $user->update(array_merge($userDetails, ['profile_image' => $profileImage]));
            return $user;
        }

        return null;
    }

    public function updateUser($UserID, array $newDetails, $roleId)
    {
        User::whereId($UserID)->update($newDetails);

        $user = User::find($UserID);

        switch ($roleId) {
            case 1:
                $user->assignRole('Admin');
                break;
            default:
                throw new InvalidArgumentException("Invalid role ID: $roleId");
        }

        $user->load([
            'roles' => function ($query) {
                $query->select('id', 'name');
            }
        ]);

        $user->roles->makeHidden('pivot');

        return $user;
    }
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function logoutUser()
    {
        $user = Auth::user();
        dd($user);
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully.'], 200);
        } else {
            return response()->json(['message' => 'No authenticated user found.'], 401);
        }

    }

    public function generateTwoFactorCode($UserID)
    {
        $authCode = [
            'two_factor_code' => rand(100000, 999999),
            'two_factor_expires_at' => now()->addMinutes(10),
        ];
        $authToken = User::whereId($UserID)->update($authCode);

        if ($authToken) {
            return $authCode;
        }
    }
    public function resetTwoFactorCode($UserID)
    {
        $authCode = [
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ];

        return User::whereId($UserID)->update($authCode);

    }

    public function deleteTenMinuteOldAuthCode($formatted)
    {
        $authCode = [
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ];

        return User::where('two_factor_expires_at', '<=', $formatted)->update($authCode);

    }

    public function verifyAuthCode($code)
    {
        return User::where('two_factor_code', $code)->first();
    }


}
