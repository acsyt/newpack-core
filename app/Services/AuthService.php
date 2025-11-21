<?php


namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\User;
use App\Models\Role;
use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;


class AuthService
{

    public function login(array $data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $remember = $data['remember'] ?? false;

        $fakeHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

        $user = null;
        $found = false;
        $anyHashCheck = false;

        $candidate = User::firstWhere('email', $email);

        if (!$candidate) {
            Hash::check($password, $fakeHash);
            $anyHashCheck = true;
        }

        if (isset($candidate->active) && !$candidate->active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated'],
            ]);
        }

        if (Hash::check($password, $candidate->password)) {
            $user = $candidate;
            $found = true;
            $anyHashCheck = true;
        } else {
            Hash::check($password, $fakeHash);
            $anyHashCheck = true;
        }

        if (!$anyHashCheck) Hash::check($password, $fakeHash);

        if (!$found) throw ValidationException::withMessages(['email' => ['Invalid username or password'],]);

        $userData = $this->getUserData($user);

        return [
            'token'         => $this->generateToken($user, $remember),
            'user'          => $userData,
            'permissions'   => $userData['permissions'],
        ];
    }

    public function getUserData($user)
    {
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

    public function getUserPermissions($user)
    {
        $role = $user->roles->first();
        if (!$role) return [];
        $permissions = $role->permissions->pluck('name');
        return $permissions->toArray();
    }

    public function getBootstrapData() {
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
            Log::error('Error validating reset token', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw new CustomException('Error validating the reset token');
        }
    }

    public function resetPassword($email, $token, $password): bool
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

            if (!$resetToken) throw new CustomException('Invalid or expired reset token');

            $user = $resetToken->resettable;

            $user = User::where('email', $email)->first();

            if (!$user) throw new CustomException('No user found with that email address');

            if (Hash::check($password, $user->password)) throw new CustomException('You cannot use your previous password');

            $user->password = $password;
            $user->save();

            $resetToken->markAsUsed();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->where('id', '!=', $resetToken->id)
                ->delete();

            $user->tokens()->delete();

            DB::commit();

            return true;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting password', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error resetting password');
        }
    }


    public function forgotPassword($email): void
    {
        try {
            DB::beginTransaction();

            $this->checkEmailVerificationLimits($email);

            $user = User::firstWhere('email', $email);

            if (!$user) throw new CustomException('No user found with the provided email address');

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

            // $this->emailService->sendPasswordResetEmail($user, $token);

            DB::commit();

            return;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating verification token', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error creating verification token');
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

            if (!$verificationToken) throw new CustomException('Invalid or expired verification token');

            $user = $verificationToken->resettable;

            if (!$user) throw new CustomException('User not found');

            $user->email_verified_at = now();
            $user->save();

            $verificationToken->markAsUsed();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
                ->where('id', '!=', $verificationToken->id)
                ->delete();

            DB::commit();

            return true;
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying email', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new CustomException('Error verifying email');
        }
    }

    private function checkPasswordResetLimits($email): void
    {
        $activeCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_PASSWORD_RESET)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->count();

        if ($activeCount >= 3) throw new CustomException('You have reached the maximum number of reset requests.');

        $recentCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_PASSWORD_RESET)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentCount >= 5) throw new CustomException('You have exceeded the hourly request limit.');

        // $lastToken = UserToken::where('email', $email)
        //     ->where('type', UserToken::TYPE_PASSWORD_RESET)
        //     ->orderBy('created_at', 'desc')
        //     ->first();
        // if ($lastToken && $lastToken->created_at->diffInMinutes(now()) < 1) throw new CustomException('You must wait 1 minute before requesting another token.');
    }

    private function checkEmailVerificationLimits($email): void
    {
        $activeCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->count();

        if ($activeCount >= 2) throw new CustomException('You already have active verification tokens.');

        $recentCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->count();

        if ($recentCount >= 3) throw new CustomException('You must wait 10 minutes between verification requests.');
    }
}
