<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class userController extends Controller
{
    public function register(Request $request){
        try {
            //validasi register
            $request->validate([
                'name' => ['required','string','max:255'],
                'username' => ['required','string','max:255','unique:users'],
                'email' => ['required','string','max:255','unique:users','email'],
                'password' => ['required','string', new Password],
            ]);
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user = User::where('email',$request->email)->first();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            
            return ResponseFormatter::success(
                [
                   'access_token' => $tokenResult,
                   'token_type' => 'Bearer',
                   'user' => $user,
                ],'user registered');
        } catch (Exception $error) {
            return ResponseFormatter::error(
                [
                   'message'=>'something went wrong',
                   'error' => $error,
                ],'Authencation failed',500
            );
        }
    }

    public function login(Request $request){
       try {
        $request->validate([
            'email' =>['required','email'],
            'password' => ['required'],
        ]);
        $crendential = request(['email','password']);
        if (!Auth::attempt($crendential)) {
            return ResponseFormatter::error([
                'message' => 'unauthorized',

            ],'authentication failed',500);
        }
        $user = User::where('email',$request->email)->first();
        if (!Hash::check($request->password,$user->password)) { // check password apakah sama dengan inputan password dicocokan kedalam tabel user yang ada di database
            throw new \Exception('invalid credential');
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return ResponseFormatter::success([
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
            'user' =>$user,

        ],'authenticated');
       } catch (Exception $error) {
        return ResponseFormatter::error([
            'message' => 'something went wrong',
            'error' => $error,
        ],'authentication failed',500);
       }
    }

    public function fetch(Request $request){
        //mengambil data user
        return ResponseFormatter::success($request->user(),'data profile user berhasil diambil');
    }

    public function updateProfile(Request $request){
        $data = $request->all();

        $user = Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'profile updated');
    }

    public function logout(Request $request){
        //mengambil data user
        $token = $request->User()->currentAccessToken()->delete();
        return ResponseFormatter::success($token,'token revoked');
    }
}
