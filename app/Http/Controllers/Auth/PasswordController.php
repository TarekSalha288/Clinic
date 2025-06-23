<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetMail;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{

    public function sendConfirmationEmail(Request $request)
    {

        $request->validate(['email' => 'required|email|exists:users,email']);
        $token = Str::random(60);
        $user=User::where('email',$request->email)->first();
        if($user){
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => now(),
            ]
        );
        $resetLink = url("/api/password/confirm");
        Mail::to($request->email)->send(new ResetMail($resetLink,$token));
        return response()->json(['message' => 'Confirmation email sent!'], 200);}
        return response()->json('You Don\'t Have Account');
    }

    public function confirmReset()
    {
        $token=request()->input('token');
        $resetRequest = DB::table('password_resets')->where('token', $token)->first();
        if (!$resetRequest) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }
        return response()->json([
            'message' => 'Are you sure you want to reset your password?',
            'email' => $resetRequest->email,
            'token' => $token
        ], 200);
    }
    public function resetPassword(Request $request)
    {

        $token=request()->input('token');
        $resetRequest = DB::table('password_resets')->where('token', $token)->first();
        if (!$resetRequest) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }
        $user = User::where('email', $resetRequest->email)->first();
        if (!$user) {
            return response()->json(['message' => 'You Don\'t Have Account'], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_resets')->where('token', $token)->delete();
        return response()->json(['message' => 'Password has been reset successfully.'], 200);
    }
}