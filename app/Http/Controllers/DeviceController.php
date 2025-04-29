<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Sender;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function getDevices(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->validate([
                'phone_id' => 'required'
            ]);
            $phone = Sender::find($request->phone_id);
            return getFonnteDevices($phone->token);
        });
    }

    public function deleteDevice(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $request->validate([
                'phone_id' => 'required'
            ]);
            $phone = Sender::find($request->phone_id);
            return getFonnteDevices($phone->token);
        });
    }
}
