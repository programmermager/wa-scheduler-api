<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Sender;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            DB::transaction(function () use ($request) {
                $request->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'country_code' => 'required|numeric',
                    'phone' => 'required|min:10|numeric',
                    'fonnte_token' => 'required',
                ]);

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                $sender = new Sender([
                    'country_code' => $request->country_code,
                    'phone' => $request->phone,
                    'token' => $request->fonnte_token,
                ]);
                $user->senders()->save($sender);
            });

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Mendaftar, silahkan login terlebih dahulu',
            ]);
        });
    }

    public function login(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
            }

            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Berhasil login',
                'data' => $user,
                'token' => $token
            ]);
        });
    }

    public function logout(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['status' => true, 'message' => 'Logged out']);
        });
    }
}
