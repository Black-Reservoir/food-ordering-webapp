<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(["message" => "Email already verified"], 200);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => "Verification link sent!"], 200);
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $request->fulfill();

        return response()->json(['message' => "Email verified successfully!"], 200);
    }
}
