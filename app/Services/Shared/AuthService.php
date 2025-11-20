<?php


namespace App\Services\Shared;

use App\Exceptions\CustomException;
use App\Models\Central\User as CentralUser;
use App\Models\Shared\Role;
use App\Models\Shared\UserToken;
use App\Models\Tenant\Resident;
use App\Models\Tenant\User;
use App\Models\Tenant;
use App\StateMachines\ResidentStateMachine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;


class AuthService
{

    private const USER_TYPES = [
        CentralUser::class  => 'central-user',
        User::class         => 'tenant-user',
    ];

    public function login(array $data, ?Tenant $tenant = null)
    {
        $email = $data['email'];
        $password = $data['password'];
        $remember = $data['remember'] ?? false;

        $models = $this->getAuthModels($tenant);
        $fakeHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

        $user = null;
        $found = false;
        $anyHashCheck = false;

        foreach ($models as $model) {
            $candidate = $model::where('email', $email)
                ->first();

            if (!$candidate) {
                Hash::check($password, $fakeHash);
                $anyHashCheck = true;
                continue;
            }

            if ($tenant && $tenant->property) {
                if (!$tenant->property->active) {
                    throw ValidationException::withMessages([
                        'username' => [__('app.modules.auth.errors.propertyInactive')],
                    ]);
                }
            }

            if (isset($candidate->active) && !$candidate->active) {
                throw ValidationException::withMessages([
                    'username' => [__('app.modules.auth.errors.accountDeactivated')],
                ]);
            }

            if (Hash::check($password, $candidate->password)) {
                $user = $candidate;
                $found = true;
                $anyHashCheck = true;
                break;
            } else {
                Hash::check($password, $fakeHash);
                $anyHashCheck = true;
            }
        }

        if (!$anyHashCheck) Hash::check($password, $fakeHash);

        if (!$found) throw ValidationException::withMessages(['username' => [__('app.modules.auth.errors.invalidCredentials')],]);

        $userData = $this->getUserData($user);

        return [
            'token'         => $this->generateToken($user, $remember),
            'user'          => $userData,
            'userType'      => $this->getUserType($user),
            'permissions'   => $userData['permissions'],
        ];
    }

    public function getUserType($user): string
    {
        return self::USER_TYPES[get_class($user)];
    }

    public function getUserData($user)
    {
        $userType = $this->getUserType($user);

        $roleNames = $user->getRoleNames() ?? [];
        $roles = Role::whereIn('name', $roleNames)->with('permissions')->get();
        $permissions = $roles->pluck('permissions')->flatten()->pluck('name')->unique();

        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'lastName'          => $user->last_name,
            'email'             => $user->email,
            'language'          => $user->language ?? 'en',
            'roles'             => $roleNames,
            'permissions'       => $permissions,
            'active'            => $user->active,
            'userType'          => $userType,
        ];
    }

    public function generateToken($model, $remember): array
    {
        $expiresAt = $remember
            ? Carbon::now()->addWeek()
            : Carbon::now()->addHours(24);
        $accessToken = $model->createToken('access_token', ['*'], $expiresAt);
        $expirationTime = Carbon::parse($accessToken->accessToken->expires_at)->toIso8601String();
        return [
            'token'     => $accessToken->plainTextToken,
            'expiresAt' => $expirationTime,
            'tokenType' => 'Bearer'
        ];
    }

    public function getAuthModels(?Tenant $tenant)
    {
        return $tenant ? [
            User::class,
        ] : [
            CentralUser::class,
        ];
    }

    public function getUserPermissions($user)
    {
        $role = $user->roles->first();
        if (!$role) return [];
        $permissions = $role->permissions->pluck('name');
        return $permissions->toArray();
    }

    public function getBootstrapData() {
        $tenant = tenant(); // ?? null

        $user = null;
        $userData = null;
        $permissions = [];

        $user = Auth::guard('sanctum')->user();
        if( $user ) {
            $userData = $this->getUserData($user);
            $permissions = $userData['permissions'];
        }

        return [
            'user'          => $userData,
            'permissions'   => $permissions,
        ];
    }

    public function validateResetToken($token, $email): bool
    {
        try {
            $resetData = UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->where('used', false)
                ->first();

            if (!$resetData || !Hash::check($token, $resetData->token)) return false;

            if ($resetData->expires_at && Carbon::now()->isAfter($resetData->expires_at)) {
                $resetData->delete();
                return false;
            }

            if (!$resetData->resettable) return false;

            return true;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al validar token de restablecimiento', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw new CustomException('Error al validar el token de restablecimiento');
        }
    }

    public function resetPassword($email, $token, $password, ?Tenant $tenant = null): bool
    {
        try {
            DB::beginTransaction();

            $resetToken = UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->get()
                ->first(function ($tokenRecord) use ($token) {
                    return Hash::check($token, $tokenRecord->token);
                });

            if (!$resetToken) throw new CustomException('Token de restablecimiento inválido o expirado');

            $user = $resetToken->resettable;

            if (!$user) {
                $models = $this->getAuthModels($tenant);
                foreach ($models as $modelClass) {
                    $candidate = $modelClass::where('email', $email)->first();
                    if ($candidate) {
                        $user = $candidate;
                        break;
                    }
                }
            }

            if (!$user) throw new CustomException('No se encontró un usuario con ese correo electrónico');

            if (Hash::check($password, $user->password)) throw new CustomException('No puede usar su contraseña anterior');

            $user->password = $password;
            $user->save();

            $resetToken->markAsUsed();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->where('id', '!=', $resetToken->id)
                ->delete();

            $user->tokens()->delete();

            DB::commit();

            Log::info('Contraseña restablecida exitosamente', [
                'email' => $email,
                'user_type' => $this->getUserType($user),
                'token_id' => $resetToken->id
            ]);

            return true;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al restablecer la contraseña', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error al restablecer la contraseña');
        }
    }

    private function findUserByEmail(string $email, ?Tenant $tenant)
    {
        $models = $this->getAuthModels($tenant);
        return collect($models)
            ->map(fn($modelClass) => $modelClass::where('email', $email)->first())
            ->filter()
            ->first();
    }

    public function forgotPassword($email, ?Tenant $tenant = null): void
    {
        try {
            DB::beginTransaction();

            $this->checkEmailVerificationLimits($email);

            $user = $this->findUserByEmail($email, $tenant);

            if (!$user) throw new CustomException('No se encontró un usuario con el correo electrónico proporcionado');

            $token = Str::random(32);

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->delete();

            $verificationToken = new UserToken([
                'email' => $email,
                'token' => Hash::make($token),
                'type' => UserToken::TYPE_PASSWORD_RESET,
            ]);

            $verificationToken->resettable()->associate($user);
            $verificationToken->save();

            // $this->emailService->sendPasswordResetEmail($user, $token, $tenant);

            DB::commit();

            Log::info('Token de verificación creado', [
                'email' => $email,
                'user_type' => $this->getUserType($user),
                'tenant_id' => $tenant?->id
            ]);

            return;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear token de verificación', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error al crear el token de verificación');
        }
    }

    public function verifyEmail($email, $token): bool
    {
        try {
            DB::beginTransaction();

            $verificationToken = UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->get()
                ->first(function ($tokenRecord) use ($token) {
                    return Hash::check($token, $tokenRecord->token);
                });

            if (!$verificationToken) throw new CustomException('Token de verificación inválido o expirado');

            $user = $verificationToken->resettable;

            if (!$user) throw new CustomException('Usuario no encontrado');

            $user->email_verified_at = now();
            $user->save();

            $verificationToken->markAsUsed();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
                ->where('id', '!=', $verificationToken->id)
                ->delete();

            DB::commit();

            Log::info('Email verificado exitosamente', [
                'email' => $email,
                'user_type' => $this->getUserType($user)
            ]);

            return true;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al verificar email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error al verificar el correo electrónico');
        }
    }

    private function checkPasswordResetLimits($email): void
    {
        $activeCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_PASSWORD_RESET)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->count();

        if ($activeCount >= 3) throw new CustomException('Has alcanzado el límite máximo de solicitudes de restablecimiento.');

        $recentCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_PASSWORD_RESET)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentCount >= 5) throw new CustomException('Has excedido el límite de solicitudes por hora.');

        // $lastToken = UserToken::where('email', $email)
        //     ->where('type', UserToken::TYPE_PASSWORD_RESET)
        //     ->orderBy('created_at', 'desc')
        //     ->first();
        // if ($lastToken && $lastToken->created_at->diffInMinutes(now()) < 1) throw new CustomException('Debes esperar 1 minuto antes de solicitar otro token.');
    }

    private function checkEmailVerificationLimits($email): void
    {
        $activeCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->count();

        if ($activeCount >= 2) throw new CustomException('Ya tienes tokens de verificación activos.');

        $recentCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->count();

        if ($recentCount >= 3) throw new CustomException('Debes esperar 10 minutos entre solicitudes de verificación.');
    }
}
