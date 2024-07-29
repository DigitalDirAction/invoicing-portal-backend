<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\TwoFactorAuthenticationRequest;
use App\Http\Requests\UserProfileRequest;
use App\Interfaces\UserRepositoryInterface;
use F9Web\ApiResponseHelpers;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;


class UserController extends Controller
{
    use ApiResponseHelpers;

    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function index(Request $request, $userId)
    {
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $userDetails = $request->validate([
                'name' => 'required',
                'email' => 'email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'company' => 'required',
                'industry' => 'required',
                'country' => 'required',
                'phone_number' => 'required',
            ]);
            dd($userDetails);
            $roleId = '1';

            $user = $this->userRepository->createUser($userDetails, $roleId);

            $auth_token = $user->createToken($user->email)->plainTextToken;
            event(new Registered($user));
            $reponse = getResponse($user, $auth_token, "User Register Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', '', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function verify(Request $request)
    {
        try {

            $user = $this->userRepository->getUserById($request->id);

            $auth_token = $user->createToken($user->email)->plainTextToken;

            if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                $reponse = getResponse('', '', "Your email address is not verified", 403);
                return $this->respondWithSuccess($reponse);
            }

            if ($user->markEmailAsVerified()) {

                $url = env('REACT_APP_URL') . '/emailverified';
                return redirect($url);

            }

            $reponse = getResponse('', '', "Your email address is not verified", 403);
            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $user = $this->userRepository->findUserByEmail($data['email']);

            if (!$user) {
                $reponse = getResponse('', '', "Oops! email does't exist", 401);
                return $this->respondWithSuccess($reponse);
            }

            if ($user['email_verified_at'] === null) {
                $reponse = getResponse('', '', "Oops! email is not verified", 401);
                return $this->respondWithSuccess($reponse);
            }

            if (!$user || !Hash::check($data['password'], $user['password'])) {
                $reponse = getResponse('', '', "Oops! Invalid email/password", 401);
                return $this->respondWithSuccess($reponse);
            } else {

                $twoFactorToken = $this->userRepository->generateTwoFactorCode($user['id']);

                Mail::send('emails/twoFactorAuthentication', ['token' => $twoFactorToken['two_factor_code'], 'user' => $user], function ($message) use ($user) {
                    $message->subject('Two-Factor Authentication Code');
                    $message->to($user['email']);
                });

                $reponse = getResponse('', '', "Login Token Sent Successfully Please Check Your Email", 200);
                return $this->respondWithSuccess($reponse);
            }

        } catch (\Exception $e) {

            $reponse = getResponse('', '', $e->getMessage(), 404);
            return $this->respondWithSuccess($reponse);
        }
    }
    public function verifyTwoAuth(TwoFactorAuthenticationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $formatted = Carbon::now()->subMinutes(3)->toDateTimeString();

            $this->userRepository->deleteTenMinuteOldAuthCode($formatted);

            $data = $request->validated();

            $checkCode = $this->userRepository->verifyAuthCode($data['code']);

            if (!$checkCode) {

                $reponse = getResponse('', '', "Oops! Your auth code has been expired. Please Click on resend for login", 403);
                return $this->respondWithSuccess($reponse);
            }

            $checkCode->load([
                'roles' => function ($query) {
                    $query->select('id', 'name');
                }
            ]);

            $checkCode->roles->makeHidden('pivot');

            $rememberMe = (bool) $data['remember_me']; // Convert to boolean
            $expiration = $rememberMe ? config('sanctum.expiration_remember') : config('sanctum.expiration');

            if ($expiration === null) {
                $auth_token = $checkCode->createToken($checkCode['id'] . ' ' . $checkCode['email'] . '-AuthToken')->plainTextToken;
            } else {
                $expirationTime = Carbon::now()->addMinutes($expiration);
                $auth_token = $checkCode->createToken($checkCode['id'] . ' ' . $checkCode['email'] . '-AuthToken', ['default'], $expirationTime)->plainTextToken;
            }

            $this->userRepository->resetTwoFactorCode($checkCode->id);

            $reponse = getResponse($checkCode, $auth_token, "User Login Successfully", 200);
            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }

    }

    public function getLoginUserData(Request $request)
    {
        try {
            $user = Auth::user();

            if (!empty($user)) {

                $user->load([
                    'roles' => function ($query) {
                        $query->select('id', 'name');
                    }
                ]);

                $user->roles->makeHidden('pivot');

                $reponse = getResponse($user, '', "User Data", 200);

            } else {
                $reponse = getResponse($user, '', "UnAuthorized", 403);
            }

            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function logout(Request $request)
    {
        try {

            auth::user()->tokens()->delete();
            $reponse = getResponse('', '', "User Logout Successfully", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse
    {
        try {

            $userDetails = $request->validated();

            if ($userDetails['email'] != $userDetails['confirm_email']) {
                $reponse = getResponse('', '', "Oops! Email & Re-Type Email does not matched", 422);
                return $this->respondWithSuccess($reponse);
            }

            $newDetails = $request->setUserData();

            $user = $this->userRepository->updateUser($userDetails['user_id'], $newDetails, $userDetails['role_id']);

            $reponse = getResponse('', '', "User Updated Successfully", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function userProfile(UserProfileRequest $request): JsonResponse
    {
        try {

            $userDetails = $request->validated();

            $profileImage = '';

            if ($request->hasFile('profile_image')) {

                $file = $request->file('profile_image');
                if ($file) {
                    $imageName = $userDetails['user_id'] . '-' . $file->getClientOriginalName();
                    $path = $file->storeAs('user_profile/profile_images', $imageName, 'public');
                    $profileImage = $path;
                }
            }

            $user = $this->userRepository->updateUserProfile($profileImage, $userDetails);

            $reponse = getResponse($user, '', "Profile Updated Successfully", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = $this->userRepository->deleteUser($request->user_id);

            $reponse = getResponse('', '', "User Deleted Successfully", 200);
            return $this->respondWithSuccess($reponse);
        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

    public function resendTwoFactorCode(Request $request, $email)
    {
        try {
            $email = base64_decode($email);

            $user = $this->userRepository->findUserByEmail($email);

            $twoFactorToken = $this->userRepository->generateTwoFactorCode($user['id']);

            Mail::send('emails/twoFactorAuthentication', ['token' => $twoFactorToken['two_factor_code'], 'user' => $user], function ($message) use ($user) {
                $message->subject('Two-Factor Authentication Code');
                $message->to($user['email']);
            });

            $reponse = getResponse('', '', "Token Resent Successfully Please Check Your Email", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }
    public function resendVerificationEmail(Request $request, $email)
    {
        try {

            $user = $this->userRepository->findUserByEmail($email);

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified.'], 400);
            }

            $user->sendEmailVerificationNotification();

            $reponse = getResponse('', '', "Verification Email Resent Successfully Please Check Your Email", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {

            $reponse = getResponse('', '', 'Oops! Something went wrong', 404);
            return $this->respondWithSuccess($reponse);
        }
    }

}
