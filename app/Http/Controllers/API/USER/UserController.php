<?php

namespace App\Http\Controllers\API\USER;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token =  $user->createToken('MyApp')->accessToken;
            return response()->json([
                'ok' => true,
                'result' => [
                    'token' => $token,
                    'message' => "Kullanıcı girişi başarılı.",
                    'user_fullname' => $user['user_fullname']
                ]
            ], $this->successStatus);
        } else {
            return response()->json([
                'ok' => false,
                'err_code' => 401,
                'description' => ' Bir sorun oluştu giriş yapılamıyor.',
            ], 401);
        }
    }
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */
    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_fullname' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'ok' => false,
                    'err_code' => 401,
                    'description' => $validator->errors(),
                ], 401);
            }
            $input = $request->all();
            $input['directory_id'] = Str::uuid();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['user_fullname'] =  $user->user_fullname;
            
            return response()->json([
                'ok' => true,
                'result' => [
                    'message' => "Kullanıcı kaydı başarılı."
                ]
            ], $this->successStatus);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'err_code' => 404,
                'description' => $e,
            ], 404);
        }
        
    }
    public function logout(Request $request)
    {   
        $accessToken = Auth::user()->token();
        
        if (isset($accessToken)) {
            $accessToken->revoke();
            return response()->json([
                'ok' => true,
                'result' => [
                    'message' => "Başarıyla çıkış yapıldı."
                ]
            ], 200);
        }
        else
            return response()->json([
                'ok' => false,
                'err_code' => 404,
                'description' => 'Kullanıcının kayıtlı bir oturumu bulunmuyor.'
            ], 401);
    }
}
