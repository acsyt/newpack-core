<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\User;
use App\Models\Role;
use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Login con protección correcta contra Timing Attacks
     */
    public function login(array $data): array {
        $email = $data['email'];
        $password = $data['password'];
        $remember = $data['remember'] ?? false;

        $user = User::with('roles.permissions')->firstWhere('email', $email);

        if (!$user || !Hash::check($password, $user->password)) {


            if (!$user) {
                Hash::check($password, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');
            }

            throw new CustomException('Invalid username or password', 401, ['email' => ['Invalid username or password']]);
        }

        if (isset($user->active) && !$user->active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated'],
            ]);
        }

        $userData = $this->getUserData($user);

        return [
            'token' => $this->generateToken($user, $remember),
            'user' => $userData,
            'permissions' => $userData['permissions'],
        ];
    }

    public function getUserData(User $user): array {

        $permissions = $this->extractPermissions($user);

        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'lastName'      => $user->last_name ?? '',
            'email'         => $user->email,
            'language'      => $user->language ?? 'en',
            'roles'         => $user->getRoleNames(),
            'permissions'   => $permissions,
            'active'        => $user->active,
        ];
    }

    public function generateToken(User $model, bool $remember): array {
        $expiresAt = $remember
            ? Carbon::now()->addWeek()
            : Carbon::now()->addHours(24);

        $accessToken = $model->createToken('access_token', ['*'], $expiresAt);

        return [
            'token' => $accessToken->plainTextToken,
            'expiresAt' => $accessToken->accessToken->expires_at->toIso8601String(),
            'tokenType' => 'Bearer'
        ];
    }

    public function getUserPermissions(User $user): array {
        return $this->extractPermissions($user)->toArray();
    }

    private function extractPermissions(User $user) {
        return $user->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('name')->unique()->values();
    }

    public function validateResetToken(string $token, string $email): bool {
        try {
            $resetToken = $this->findValidToken($email, $token, UserToken::TYPE_PASSWORD_RESET);

            if (!$resetToken) return false;
            if (!$resetToken->resettable) return false;

            return true;

        } catch (\Exception $e) {
            Log::error('Error validating reset token', ['email' => $email, 'error' => $e->getMessage()]);

            return false;
        }
    }

    public function resetPassword(string $email, string $token, string $password): bool {
        try {
            DB::beginTransaction();

            $resetToken = $this->findValidToken($email, $token, UserToken::TYPE_PASSWORD_RESET);

            if (!$resetToken) throw new CustomException('Invalid or expired reset token');

            $user = $resetToken->resettable;

            if (!$user) throw new CustomException('No user found with that email address');

            if (Hash::check($password, $user->password)) {
                throw new CustomException('You cannot use your previous password');
            }

            $user->password = Hash::make($password);
            $user->save();

            $resetToken->markAsUsed();


            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->delete();

            $user->tokens()->delete();

            DB::commit();
            return true;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting password', ['email' => $email, 'error' => $e->getMessage()]);
            throw new CustomException('Error resetting password');
        }
    }

    public function forgotPassword(string $email): void {
        try {
            $this->checkPasswordResetLimits($email);

            $user = User::firstWhere('email', $email);
            if (!$user) throw new CustomException('No user found with the provided email address');

            DB::beginTransaction();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_PASSWORD_RESET)
                ->delete();

            $plainToken = Str::random(32);

            $verificationToken = new UserToken([
                'email' => $email,
                'token' => Hash::make($plainToken),
                'type' => UserToken::TYPE_PASSWORD_RESET,
                'created_at' => now(),
                'expires_at' => now()->addHour(),
            ]);

            $verificationToken->resettable()->associate($user);
            $verificationToken->save();

            // $this->emailService->sendPasswordResetEmail($user, $plainToken);

            DB::commit();

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating verification token', ['email' => $email, 'error' => $e->getMessage()]);
            throw new CustomException('Error creating verification token');
        }
    }

    public function verifyEmail(string $email, string $token): bool {
        try {
            DB::beginTransaction();

            $verificationToken = $this->findValidToken($email, $token, UserToken::TYPE_EMAIL_VERIFICATION);

            if (!$verificationToken) throw new CustomException('Invalid or expired verification token');

            $user = $verificationToken->resettable;
            if (!$user) throw new CustomException('User not found');

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
                ->delete();

            DB::commit();
            return true;

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying email', ['email' => $email, 'error' => $e->getMessage()]);
            throw new CustomException('Error verifying email');
        }
    }

    private function findValidToken(string $email, string $plainToken, string $type): ?UserToken {
        $candidates = UserToken::where('email', $email)
            ->where('type', $type)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->get();

        return $candidates->first(function ($tokenRecord) use ($plainToken) {
            return Hash::check($plainToken, $tokenRecord->token);
        });
    }

    private function checkPasswordResetLimits(string $email): void {
        $recentCount = UserToken::where('email', $email)
            ->where('type', UserToken::TYPE_PASSWORD_RESET)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= 5) {
            throw new CustomException('You have exceeded the hourly request limit.');
        }
    }


    public function resendVerificationEmail(string $email): void {
        try {
            $this->checkEmailVerificationLimits($email);

            $user = User::firstWhere('email', $email);

            if (!$user) throw new CustomException('User not found');
            if ($user->hasVerifiedEmail()) throw new CustomException('Email already verified');

            DB::beginTransaction();

            UserToken::where('email', $email)
                ->where('type', UserToken::TYPE_EMAIL_VERIFICATION)
                ->delete();

            $plainToken = Str::random(32);

            $verificationToken = new UserToken([
                'email' => $email,
                'token' => Hash::make($plainToken),
                'type' => UserToken::TYPE_EMAIL_VERIFICATION,
                'created_at' => now(),
                'expires_at' => now()->addHours(24),
            ]);

            $verificationToken->resettable()->associate($user);
            $verificationToken->save();

            // $this->emailService->sendVerificationEmail($user, $plainToken);

            DB::commit();

        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending verification email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw new CustomException('Error sending verification email');
        }
    }

    private function checkEmailVerificationLimits(string $email): void {
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
