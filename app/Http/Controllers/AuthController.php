<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ReactivateAccountRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ValidateTokenRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\VerifyEmailTokenRequest;
use App\Http\Requests\Auth\SendResetLinkEmailRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request) {
        $data = $request->validated();
        return [
            'data'      => $this->authService->login($data),
            'message'   => 'Logged in successfully'
        ];
    }

    public function getUser() {
        $user = Auth::user();
        return $this->authService->getUserData($user);
    }

    public function bootstrap() {
        try {
            return $this->authService->getBootstrapData();
        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Error loading application data: " . $e->getMessage());
            throw new CustomException("Error loading application data");
        }
    }

    public function logout(Request $request) {
        $user = $request->user();

        if (!$user) return response()->json([
            'message' => 'User not authenticated'
        ], 401);

        $user->currentAccessToken()->delete();

        return response()->json([
            'data' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function getPermissions() {
        $user = Auth::user();
        return $this->authService->getUserPermissions($user);
    }

    public function forgotPassword(ForgotPasswordRequest $request) {
        $data = $request->validated();
        $this->authService->forgotPassword($data['email']);
        return [
            'data'      => true,
            'message'   => 'We have sent an email with instructions to recover your password.',
        ];
    }

    public function sendResetLink(SendResetLinkEmailRequest $request) {
        $data    = $request->validated();

        $this->authService->forgotPassword($data['email']);

        return [
            'data'      => true,
            'message'   => 'We have sent an email with instructions to reset your password.',
        ];
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $data = $request->validated();

        $this->authService->resetPassword(
            $data['email'],
            $data['token'],
            $data['password'],
        );

        return [
            'data'      => true,
            'message'   => 'Your password has been reset successfully.'
        ];
    }

    public function validateToken(ValidateTokenRequest $request) {
        $data = $request->validated();

        $result = $this->authService->validateResetToken($data['token'], $data['email']);

        return [
            'data'      => $result,
        ];
    }

    public function sendVerificationEmail(VerifyEmailRequest $request) {
        $data = $request->validated();

        $this->authService->forgotPassword($data['email']);

        return [
            'data'      => true,
            'message'   => 'We have sent an email with instructions to verify your email address.',
        ];
    }

    public function verifyEmailWithToken(VerifyEmailTokenRequest $request) {
        $data = $request->validated();

        $this->authService->verifyEmail($data['email'], $data['token']);

        return [
            'data'      => true,
            'message'   => 'Email verified successfully'
        ];
    }

    public function verifyEmail(Request $request, $id, $hash) {
        if (!$request->hasValidSignature()) throw new CustomException('Invalid verification link');

        $user = User::find( $id );

        if (!$user) throw new CustomException('User not found');

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) throw new CustomException('Invalid verification link');

        if ($user->hasVerifiedEmail()) return [
            'data'    => true,
            'message' => 'Email already verified'
        ];

        $user->markEmailAsVerified();

        return [
            'data'      => true,
            'message'   => 'Email verified successfully'
        ];
    }

}
