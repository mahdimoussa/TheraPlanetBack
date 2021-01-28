<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Message;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{

    public function forget(Request $request){
        $email = $request->email;
        if (User::where('email','=',$email)->doesntExist()){
            return response([
                'message' => 'User does not exist'
            ],404);
        }

        $oldToken = DB::table('password_resets')->where('email', $email)->first()->token;
        if ($oldToken){
            $token = $oldToken;
        }
        else{
            $token = Str::random(10);
        }
        try {
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token
            ]);


            Mail::to($email)->send(new ResetPasswordMail($token));
            return response([
                'message' => 'Check your email'
            ]);
        }catch (\Exception $exception){
            return response([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function resetpassword(Request $request){
        $user = User::where('email','=',$request->data['email'])->first();
        $user->update(['password' =>bcrypt($request->data['password'])]);
        return response()->json(['message' => 'Password Successfully Changed']);
    }
}
