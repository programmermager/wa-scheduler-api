<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $limit = $request->per_page ?? 10;
            $contacts = Contact::orderBy('name', 'asc')->paginate($limit);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Kontak',
                'data' => $contacts,
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
                'country_code' => 'required',
                'phone' => 'required|numeric',
                'name' => 'required|string',
            ]);
            Contact::create([
                'user_id' => auth()->user()->id,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambahkan data Kontak',

            ]);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return ExceptionHandler::handle(function () use ($id) {
            $contact = Contact::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Kontak',
                'data' => $contact,
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
                'name' => 'required|string',
            ]);

            $contact = Contact::findOrFail($id);
            $contact->update([
                'country_code' => $request->country_code,
                'phone' => $request->phone,
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil memperbarui data Kontak',
            ]);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return ExceptionHandler::handle(function () use ($id) {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data Kontak',
            ]);
        });
    }
}
