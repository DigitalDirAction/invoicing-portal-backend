<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user = $this->userRepository->getAllUsers();

            $reponse = getResponse($user, '', "Users List", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $userDetails = $request->validate([
                'name' => 'required|string|unique:users,name',
                'email' => 'required|email|unique:users,email|max:255',
                'password' => 'required|string|min:8|confirmed',
                'company' => 'required|string|max:255',
                'industry' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20|regex:/^[0-9+\(\)#\.\s\/ext-]+$/',
            ]);

            $roleId = '1';

            $user = $this->userRepository->createUser($userDetails, $roleId);
            $auth_token = $user->createToken($user->email)->plainTextToken;

            event(new Registered($user));

            $response = getResponse($user, $auth_token, "User Register Successfully", 201);
            return $this->respondWithSuccess($response);

        } catch (ValidationException $e) {
            $response = getResponseIfValidationFailed($e->errors(), '', 'Validation failed', 422);
            return $this->respondWithSuccess($response);

        } catch (\Exception $e) {
            $response = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($response);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }

    }

    public function logoutUser(Request $request)
    {
        try {
            // Logout the authenticated user
            Auth::logout();
            $user = Auth::user();

            if ($user) {
                $user->tokens()->delete();

            } else {
                $response = getResponse('', '', "No authenticated user found.", 401);
            }
            $response = getResponse('', '', "Logged out successfully", 200);
            return $this->respondWithSuccess($response);

        } catch (\Exception $e) {
            $response = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($response);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request): JsonResponse
    {
        try {

            $user = Auth::user();

            $reponse = getResponse($user, '', "User Data", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $userDetails = $request->validate([
                'user_id' => 'required',
                'name' => 'required|string|unique:users,name',
                'email' => 'required|email|unique:users,email,' . $request->user_id,
                'company' => 'required|string|max:255',
                'industry' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20|regex:/^[0-9+\(\)#\.\s\/ext-]+$/',
            ]);

            $roleId = '1';
            $userID = $request->user_id;

            $user = $this->userRepository->updateUser($userID, $userDetails, $roleId);

            $reponse = getResponse($user, '', "User Updated Successfully", 201);
            return $this->respondWithSuccess($reponse);

        } catch (ValidationException $e) {
            $response = getResponseIfValidationFailed($e->errors(), '', 'Validation failed', 422);
            return $this->respondWithSuccess($response);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $customerID)
    {
        try {

            $user = $this->userRepository->deleteUser($customerID);

            $reponse = getResponse($user, '', "User Deleted Successfully", 200);
            return $this->respondWithSuccess($reponse);

        } catch (\Exception $e) {
            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
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

            $reponse = getResponse('', '', 'Oops! Something went wrong', 500);
            return $this->respondWithSuccess($reponse);
        }
    }
}
