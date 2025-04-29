<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Sender;
use Illuminate\Http\Request;

class SenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $limit = $request->per_page ?? 10;
            $senders = Sender::orderBy('created_at', 'desc')->where('user_id', auth()->user()->id)->paginate($limit);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Pengirim',
                'data' => $senders,
            ]);
        });
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->validate([
                'country_code' => 'required|numeric',
                'phone' => 'required|numeric|min:10',
                'fonnte_token' => 'required|string',
            ]);

            $user = auth()->user();

            Sender::create([
                'user_id' => $user->id,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'token' => $request->fonnte_token,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan data Pengirim',
            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return ExceptionHandler::handle(function () use ($id) {
            $sender = Sender::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Pengirim',
                'data' => $sender,
            ]);
        });
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return ExceptionHandler::handle(function () use ($request, $id) {
            $request->validate([
                'country_code' => 'required',
                'phone' => 'required|numeric',
                'fonnte_token' => 'required|string',
            ]);

            $contact = Sender::findOrFail($id);
            $contact->update([
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'token' => $request->fonnte_token,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil memperbarui data Pengirim',
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return ExceptionHandler::handle(function () use ($id) {
            $contact = Sender::findOrFail($id);
            $contact->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data Pengirim',
            ]);
        });
    }
}
