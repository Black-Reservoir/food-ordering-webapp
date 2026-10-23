<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function sendResetLink(ForgotPasswordRequest $request)
    {
        $data = $request->validated();

        $status = Password::sendResetLink($data);

        return response()->json([
            'message' => 'If the email exists, a password reset link has been sent.'
        ], 200);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $credentials = $request->validated();
        $resetUser = null;

        try {
            $status = Password::reset(
                $credentials,
                function ($user, $password) use (&$resetUser) {

                    $resetUser = $user;
                    DB::transaction(function () use ($user, $password) {

                        $user->forceFill([
                            'password' => Hash::make($password),
                            'remember_token' => Str::random(60),
                        ])->save();

                        $user->tokens()->delete();
                    });
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                try {
                    if ($resetUser instanceof User) {

                        event(new PasswordReset($resetUser));
                    }
                } catch (Exception $e) {
                    Log::error('Failed to dispatch PasswordReset event.', [
                        'exception' => $e->getMessage(),
                    ]);
                }
                return response()->json(['message' => __($status)], 200);
            } else {
                return response()->json(['message' => __($status)], 422);
            }
        } catch (Exception $e) {
            Log::error('An unexpected error occurred while resetting the password.', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while resetting the password.'
            ], 500);
        }
    }
}
