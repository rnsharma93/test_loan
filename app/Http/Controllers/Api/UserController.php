<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Hash;
class UserController extends Controller
{
    //create new user
    /* 
    Params 
        name : string
        email : string
        password : string
    Return (JSON)
        message : string (success message)
        token : string (Authorization token)
    */
    public function create(Request $request) {
        $this->validate($request,[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user_data['name'] = $request->get('name');
        $user_data['email'] = $request->get('email');
        $user_data['password'] = Hash::make( $request->get('password') );

        $user = User::create($user_data);
        $token = $user->createToken('api-token');
        //$user->save();

        return ['message'=>'user created successfully','token'=>$token->plainTextToken];
    }

    //Login
    /**
     * Params
     *  email, password
     * Return 
     *  Json => message, token
     */

    public function login(Request $request) {
        $this->validate($request,[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        
        $token = $user->createToken('api-token')->plainTextToken;

        return ['message'=>'success','token'=>$token];
    }
}
