<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Sender;
use Illuminate\Http\Request;

class SenderController extends Controller
{
    public function store(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->validate([
                'country_code' => 'required|numeric',
                'phone' => 'required|numeric|min:10',
                'fonnte_token' => 'required|string',
            ]);

            $user = auth()->user();

            $sender = Sender::create([
                'user_id' => $user->id,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'token' => $request->fonnte_token,
            ]);

            return response()->json($sender);
        });
    }
}
