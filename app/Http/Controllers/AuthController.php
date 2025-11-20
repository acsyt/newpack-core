<?php

namespace App\Http\Controllers\Shared;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shared\Auth\ForgotPasswordRequest;
use App\Http\Requests\Shared\Auth\LoginRequest;
use App\Http\Requests\Shared\Auth\ReactivateAccountRequest;
use App\Http\Requests\Shared\Auth\ResetPasswordRequest;
use App\Http\Requests\Shared\Auth\ValidateTokenRequest;
use App\Http\Requests\Shared\Auth\VerifyEmailRequest;
use App\Http\Requests\Shared\Auth\VerifyEmailTokenRequest;
use App\Http\Requests\Shared\Auth\SendResetLinkEmailRequest;
use App\Services\Shared\AuthService;
use App\Services\Central\TranslationService;
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
        $tenant = tenant();
        return [
            'data'      => $this->authService->login($data, $tenant),
            'message'   => __('app.pages.auth.login.toasts.success')
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
            'message' => 'Usuario no autenticado'
        ], 401);

        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ], 200);
    }

    public function getPermissions() {
        $user = Auth::user();
        return $this->authService->getUserPermissions($user);
    }

    public function forgotPassword(ForgotPasswordRequest $request) {
        $data = $request->validated();
        $tenant = tenant();
        $this->authService->forgotPassword($data['email'], $tenant);
        return [
            'data'      => true,
            'message'   => 'Hemos enviado un correo con instrucciones para recuperar tu contraseña.',
        ];
    }

    public function sendResetLink(SendResetLinkEmailRequest $request) {
        $data    = $request->validated();
        $tenant  = tenant();

        $this->authService->forgotPassword($data['email'], $tenant);

        return [
            'data'      => true,
            'message'   => 'Hemos enviado un correo con instrucciones para restablecer tu contraseña.',
        ];
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $data = $request->validated();
        $tenant = tenant();

        $this->authService->resetPassword(
            $data['email'],
            $data['token'],
            $data['password'],
            $tenant
        );

        return [
            'data'      => true,
            'message'   => 'Tu contraseña ha sido restablecida con éxito.'
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
        $tenant = tenant();

        $this->authService->forgotPassword($data['email'], $tenant);

        return [
            'data'      => true,
            'message'   => 'Hemos enviado un correo con instrucciones para verificar tu email.',
        ];
    }

    public function verifyEmailWithToken(VerifyEmailTokenRequest $request) {
        $data = $request->validated();

        $this->authService->verifyEmail($data['email'], $data['token']);

        return [
            'data'      => true,
            'message'   => 'Email verificado exitosamente'
        ];
    }

    public function verifyEmail(Request $request, $id, $hash) {
        if (!$request->hasValidSignature()) throw new CustomException('Enlace de verificación inválido');

        $tenant = tenant();
        $models = $this->authService->getAuthModels($tenant);
        $user = null;

        foreach ($models as $modelClass) {
            $candidate = $modelClass::find($id);
            if ($candidate) {
                $user = $candidate;
                break;
            }
        }

        if (!$user) throw new CustomException('Usuario no encontrado');

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) throw new CustomException('Enlace de verificación inválido');

        if ($user->hasVerifiedEmail()) return [
            'data'    => true,
            'message' => 'Email ya verificado'
        ];

        $user->markEmailAsVerified();

        return [
            'data'      => true,
            'message'   => 'Email verificado exitosamente'
        ];
    }

}
