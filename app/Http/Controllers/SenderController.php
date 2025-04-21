<?php

namespace App\Http\Controllers;

use App\Models\Sender;
use Illuminate\Http\Request;

class SenderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'token' => 'required',
        ]);

        $sender = Sender::create([
            'user_id' => $request->user()->id,
            'number' => $request->number,
            'token' => $request->token,
        ]);

        return response()->json($sender);
    }
}
