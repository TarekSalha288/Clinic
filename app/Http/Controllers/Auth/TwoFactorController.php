<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorMail;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

class TwoFactorController extends Controller
{
    public function varify(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make(request()->all(), [
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (auth()->check()) {
            if ($user->expire_at < now()) {
                $user->delete();
                return response()->json(['timeout! sorry this code is not working please sign up again'], 400);
            }
        }

        if ($request->code == $user->code) {
            $user->resetTwoFactorCode();
            return response()->json(['thank you, you enter a correct code'], 200);
        } else {
            return response()->json(['sorry you enter uncorect code'], 400);
        }

    }
    public function resendCode()
    {
        $user = auth()->user();
        if (auth()->check()) {
            if ($user->expire_at < now()) {
                $user->delete();
                return response()->json(['timeout! sorry this code is not working please sign up again']);
            }
        }
        $user->generateCode();
        Mail::to($user->email)->send(new TwoFactorMail($user->code, $user->name));
        return response()->json(['your varification code resend to your email successfully'], 200);
    }
}
