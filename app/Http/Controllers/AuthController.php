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

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @OA\Post(
     *          path="/api/auth/login",
     *          tags={"Authentication"},
     *          summary="Iniciar sesión",
     *          description="Autentica un usuario y retorna un token de acceso",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email","password"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com"),
     *                  @OA\Property(property="password", type="string", format="password", example="123456"),
     *                  @OA\Property(property="remember", type="boolean", example=false)
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Login exitoso",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="object",
     *                      @OA\Property(property="user", type="object"),
     *                      @OA\Property(property="token", type="string", example="1|abc123...")
     *                  ),
     *                  @OA\Property(property="message", type="string", example="Logged in successfully")
     *              )
     *          ),
     * @OA\Response(response=401, description="Credenciales inválidas"),
     * @OA\Response(response=422, description="Error de validación")
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
     *          path="/api/auth/user",
     *          tags={"Authentication"},
     *          summary="Obtener usuario autenticado",
     *          description="Retorna la información del usuario actualmente autenticado",
     *          security={{"sanctum": {}}},
     *          @OA\Response(
     *              response=200,
     *              description="Datos del usuario",
     *              @OA\JsonContent(type="object")
     *          ),
     *          @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function getUser()
    {
        $user = Auth::user();
        return $this->authService->getUserData($user);
    }

    /**
     * @OA\Post(
     *          path="/api/auth/logout",
     *          tags={"Authentication"},
     *          summary="Cerrar sesión",
     *          description="Invalida el token de acceso actual del usuario",
     *          security={{"sanctum": {}}},
     *          @OA\Response(
     *              response=200,
     *              description="Sesión cerrada exitosamente",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string", example="Successfully logged out")
     *              )
     *          ),
     *          @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user)
            return response()->json([
                'message' => 'User not authenticated'
            ], 401);

        $user->currentAccessToken()->delete();

        return response()->json([
            'data' => true,
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function getPermissions()
    {
        $user = Auth::user();
        return $this->authService->getUserPermissions($user);
    }

    /**
     * @OA\Post(
     *          path="/api/auth/forgot-password",
     *          tags={"Password Reset"},
     *          summary="Solicitar recuperación de contraseña",
     *          description="Envía un email con instrucciones para recuperar la contraseña",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Email enviado",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string")
     *              )
     *          ),
     *          @OA\Response(response=422, description="Error de validación")
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
     *          path="/api/auth/send-reset-link",
     *          tags={"Password Reset"},
     *          summary="Enviar link de reset",
     *          description="Envía email con link para resetear contraseña",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Link enviado",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string")
     *              )
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
     *          path="/api/auth/reset-password",
     *          tags={"Password Reset"},
     *          summary="Resetear contraseña",
     *          description="Resetea la contraseña del usuario usando el token recibido por email",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email","token","password","password_confirmation"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com"),
     *                  @OA\Property(property="token", type="string", example="abc123token456"),
     *                  @OA\Property(property="password", type="string", format="password", example="new123456"),
     *                  @OA\Property(property="password_confirmation", type="string", format="password", example="new123456")
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Contraseña reseteada",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string")
     *              )
     *          ),
     *          @OA\Response(response=422, description="Token inválido o expirado")
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();

        $this->authService->resetPassword(
            $data['email'],
            $data['token'],
            $data['password'],
        );

        return [
            'data' => true,
            'message' => 'Your password has been reset successfully.'
        ];
    }

    /**
     * @OA\Get(
     *          path="/api/auth/validate-token",
     *          tags={"Password Reset"},
     *          summary="Validar token de reset",
     *          description="Valida si un token de reset de contraseña es válido y no ha expirado",
     *          @OA\Parameter(
     *              name="token",
     *              in="query",
     *              required=true,
     *              @OA\Schema(type="string")
     *          ),
     *          @OA\Parameter(
     *              name="email",
     *              in="query",
     *              required=true,
     *              @OA\Schema(type="string", format="email")
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Token válido",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true)
     *              )
     *          ),
     *          @OA\Response(response=422, description="Token inválido")
     * )
     */
    public function validateToken(ValidateTokenRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->validateResetToken($data['token'], $data['email']);

        return [
            'data' => $result,
        ];
    }

    /**
     * @OA\Post(
     *          path="/api/auth/send-verification-email",
     *          tags={"Email Verification"},
     *          summary="Enviar email de verificación",
     *          description="Envía un email con link para verificar la dirección de correo",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com")
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Email enviado",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string")
     *              )
     * )
     * )
     */
    public function sendVerificationEmail(VerifyEmailRequest $request)
    {
        $data = $request->validated();

        $this->authService->resendVerificationEmail($data['email']);

        return [
            'data' => true,
            'message' => 'We have sent an email with instructions to verify your email address.',
        ];
    }

    /**
     * @OA\Post(
     *          path="/api/auth/verify-email-token",
     *          tags={"Email Verification"},
     *          summary="Verificar email con token",
     *          description="Verifica el email del usuario usando un token",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\JsonContent(
     *                  required={"email","token"},
     *                  @OA\Property(property="email", type="string", format="email", example="admin@acsyt.com"),
     *                  @OA\Property(property="token", type="string", example="verification-token")
     *              )
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Email verificado",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string", example="Email verified successfully")
     *              )
     *          ),
     *          @OA\Response(response=422, description="Token inválido")
     * )
     */
    public function verifyEmailWithToken(VerifyEmailTokenRequest $request)
    {
        $data = $request->validated();

        $this->authService->verifyEmail($data['email'], $data['token']);

        return [
            'data' => true,
            'message' => 'Email verified successfully'
        ];
    }

    /**
     * @OA\Get(
     *          path="/api/auth/verify-email/{id}/{hash}",
     *          tags={"Email Verification"},
     *          summary="Verificar email con link firmado",
     *          description="Verifica el email usando un link firmado enviado por email",
     *          @OA\Parameter(
     *              name="id",
     *              in="path",
     *              required=true,
     *              description="ID del usuario",
     *              @OA\Schema(type="integer")
     *          ),
     *          @OA\Parameter(
     *              name="hash",
     *              in="path",
     *              required=true,
     *              description="Hash de verificación",
     *              @OA\Schema(type="string")
     *          ),
     *          @OA\Parameter(
     *              name="signature",
     *              in="query",
     *              required=true,
     *              description="Firma del link (generada automáticamente)",
     *              @OA\Schema(type="string")
     *          ),
     *          @OA\Response(
     *              response=200,
     *              description="Email verificado",
     *              @OA\JsonContent(
     *                  @OA\Property(property="data", type="boolean", example=true),
     *                  @OA\Property(property="message", type="string")
     *              )
     *          ),
     *          @OA\Response(response=400, description="Link inválido o expirado"),
     *          @OA\Response(response=404, description="Usuario no encontrado")
     *      )
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
}
