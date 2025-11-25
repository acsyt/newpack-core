<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ValidateTokenRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\VerifyEmailTokenRequest;
use App\Http\Requests\Auth\SendResetLinkEmailRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @OA\Schema(
 * schema="ValidationErrors",
 * title="Errores de Validación",
 * @OA\Property(property="message", type="string", example="The given data was invalid."),
 * @OA\Property(property="errors", type="object",
 * @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
 * )
 * )
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * tags={"Authentication"},
     * summary="Iniciar sesión",
     * description="Autentica un usuario y retorna token, datos completos del usuario y permisos",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com"),
     * @OA\Property(property="password", type="string", format="password", example="123456"),
     * @OA\Property(property="remember", type="boolean", example=false)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login exitoso",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="token", type="object",
     * @OA\Property(property="token", type="string", format="token", example="1|abc123..."),
     * @OA\Property(property="expiresAt", type="string", format="date-time", example="2025-11-21T15:01:50-06:00"),
     * @OA\Property(property="tokenType", type="string", format="tokenType", example="Bearer")
     * ),
     * @OA\Property(property="user", type="object",
     * description="Objeto completo del usuario, idéntico al endpoint /user",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="Admin"),
     * @OA\Property(property="lastName", type="string", example="System"),
     * @OA\Property(property="email", type="string", example="admin@acsyt.com"),
     * @OA\Property(property="language", type="string", example="en"),
     * @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin"}),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"users.create"}),
     * @OA\Property(property="active", type="boolean", example=true)
     * ),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"users.create", "users.view"})
     * ),
     * @OA\Property(property="message", type="string", example="Logged in successfully")
     * )
     * ),
     * @OA\Response(response=401, description="Credenciales inválidas"),
     * @OA\Response(
     * response=422,
     * description="Error de validación",
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     * )
     * )
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        return [
            'data' => $this->authService->login($data),
            'message' => 'Logged in successfully'
        ];
    }

    /**
     * @OA\Get(
     * path="/api/auth/user",
     * tags={"Authentication"},
     * summary="Obtener usuario autenticado",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Datos del usuario",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John"),
     * @OA\Property(property="lastName", type="string", example="Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     * @OA\Property(property="language", type="string", example="en"),
     * @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"admin"}),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"users.create", "users.view"}),
     * @OA\Property(property="active", type="boolean", example=true)
     * )
     * )
     * ),
     * @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function getUser()
    {
        $user = Auth::user();
        return [
            'data' => $this->authService->getUserData($user)
        ];
    }

    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * tags={"Authentication"},
     * summary="Cerrar sesión",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Sesión cerrada",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string", example="Successfully logged out")
     * )
     * ),
     * @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user)
            return response()->json(['message' => 'User not authenticated'], 401);

        $user->currentAccessToken()->delete();

        return response()->json([
            'data' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    /**
     * @OA\Get(
     * path="/api/auth/permissions",
     * tags={"Authentication"},
     * summary="Obtener permisos del usuario",
     * security={{"sanctum": {}}},
     * @OA\Response(
     * response=200,
     * description="Lista de permisos",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="array", @OA\Items(type="string", example="users.create"))
     * )
     * )
     * )
     */
    public function getPermissions()
    {
        $user = Auth::user();
        return [
            'data' => $this->authService->getUserPermissions($user)
        ];
    }

    /**
     * @OA\Post(
     * path="/api/auth/forgot-password",
     * tags={"Password Reset"},
     * summary="Solicitar recuperación de contraseña",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Email enviado",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Error de validación",
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     * )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();
        $this->authService->forgotPassword($data['email']);
        return [
            'data' => true,
            'message' => 'We have sent an email with instructions to recover your password.',
        ];
    }

    /**
     * @OA\Post(
     * path="/api/auth/send-reset-link",
     * tags={"Password Reset"},
     * summary="Enviar link de reset (Alias)",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Link enviado",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string")
     * )
     * )
     * )
     */
    public function sendResetLink(SendResetLinkEmailRequest $request)
    {
        $data = $request->validated();
        $this->authService->forgotPassword($data['email']);
        return [
            'data' => true,
            'message' => 'We have sent an email with instructions to reset your password.',
        ];
    }

    /**
     * @OA\Post(
     * path="/api/auth/reset-password",
     * tags={"Password Reset"},
     * summary="Resetear contraseña",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","token","password","password_confirmation"},
     * @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com"),
     * @OA\Property(property="token", type="string", example="abc123token..."),
     * @OA\Property(property="password", type="string", format="password", example="new123456"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="new123456")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Contraseña reseteada",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string")
     * )
     * ),
     * @OA\Response(response=422, description="Token inválido o datos incorrectos")
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $this->authService->resetPassword($data['email'], $data['token'], $data['password']);
        return [
            'data' => true,
            'message' => 'Your password has been reset successfully.'
        ];
    }

    /**
     * @OA\Get(
     * path="/api/auth/validate-token",
     * tags={"Password Reset"},
     * summary="Validar token de reset",
     * @OA\Parameter(name="token", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string", format="email")),
     * @OA\Response(
     * response=200,
     * description="Resultado validación",
     * @OA\JsonContent(@OA\Property(property="data", type="boolean", example=true))
     * )
     * )
     */
    public function validateToken(ValidateTokenRequest $request)
    {
        $data = $request->validated();
        $result = $this->authService->validateResetToken($data['token'], $data['email']);
        return ['data' => $result];
    }

    /**
     * @OA\Post(
     * path="/api/auth/send-verification-email",
     * tags={"Email Verification"},
     * summary="Reenviar email de verificación",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email"},
     * @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Email enviado",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string")
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Error de validación",
     * @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     * )
     * )
     */
    public function resendVerificationEmail(VerifyEmailTokenRequest $request)
    {
        $data = $request->validated();
        $this->authService->resendVerificationEmail($data['email']);
        return [
            'data' => true,
            'message' => 'Verification email sent successfully'
        ];
    }

    /**
     * @OA\Get(
     * path="/api/auth/verify-email/{id}/{hash}",
     * tags={"Email Verification"},
     * summary="Verificar email (Link)",
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="signature", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Response(
     * response=200,
     * description="Email verificado",
     * @OA\JsonContent(
     * @OA\Property(property="data", type="boolean", example=true),
     * @OA\Property(property="message", type="string")
     * )
     * ),
     * @OA\Response(response=400, description="Link inválido o expirado")
     * )
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        if (!$request->hasValidSignature()) throw new CustomException('Invalid verification link');

        $user = User::find($id);
        if (!$user) throw new CustomException('User not found');

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new CustomException('Invalid verification link');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return [
            'data' => true,
            'message' => 'Email verified successfully'
        ];
    }

    /**
     * @OA\Post(
     *     path="/api/auth/verify-email-token",
     *     tags={"Email Verification"},
     *     summary="Verificar email con token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "token"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="verification_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verificado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verificado exitosamente")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Token inválido o expirado"),
     *     @OA\Response(response=404, description="Usuario no encontrado"),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrors")
     *     )
     * )
     */
    public function verifyEmailWithToken(VerifyEmailTokenRequest $request)
    {
        $email = $request->validated('email');
        $token = $request->validated('token');

        $this->authService->verifyEmail($email, $token);

        return response()->json([
            'data' => true,
            'message' => 'Email verificado exitosamente'
        ]);
    }
}
