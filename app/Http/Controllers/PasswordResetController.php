<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Models\PasswordReset;
use App\Http\Requests\ForgotUserPasswordRequest;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Interfaces\PasswordResetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use F9Web\ApiResponseHelpers;
use Carbon\Carbon;


class PasswordResetController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(private PasswordResetRepositoryInterface $passwordResetRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function forgotPassword(ForgotUserPasswordRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $email = $data['email'];
            $this->passwordResetRepository->deleteToken($email);
            $user = $this->userRepository->findUserByEmail($email);
            if (!$user) {
                $reponse = getResponse('', '', "Email not found", 401);
                return $this->respondWithSuccess($reponse);
            }

            $token = Str::random(60);

            $tokenDetail = [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ];

            $this->passwordResetRepository->createToken($tokenDetail);

            Mail::send('emails/resetPassword', ['token' => $token], function ($message) use ($email) {
                $message->subject('Reset Password Request');
                $message->to($email);
            });

            $reponse = getResponse('', '', "Password Reset Email Sent... Check Your Email", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($reponse);
        }
    }
    public function resetPasswordForm(Request $request, $token)
    {
        try {
            $user = $this->userRepository->findUserByEmail($token);

            if ($user) {
                view('reset_password', compact('user'));
            } else {

                $reponse = getResponse('', '', "Oops! Your are unable to reset", 403);
                return $this->respondWithSuccess($reponse);
            }
        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }

    }

    public function resetPassword(ResetUserPasswordRequest $request, $token)
    {
        try {
            $formatted = Carbon::now()->subMinutes(5)->toDateTimeString();

            $this->passwordResetRepository->deleteFiveMinuteOldToken($formatted);

            $data = $request->validated();

            $passwordreset = $this->passwordResetRepository->verifyToken($token);

            if (!$passwordreset) {

                $reponse = getResponse('', '', "Oops! Your token has been expired", 403);
                return $this->respondWithSuccess($reponse);
            }

            $user = $this->userRepository->findUserByEmail($passwordreset->email);

            $user->password = Hash::make($data['password']);
            $user->save();

            $this->passwordResetRepository->deleteToken($user->email);

            $reponse = getResponse($user, '', "Password Reset Successfully", 200);
            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }
}
