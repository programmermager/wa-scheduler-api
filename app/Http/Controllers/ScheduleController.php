<?php

namespace App\Http\Controllers;

use App\Helpers\ExceptionHandler;
use App\Models\Schedule;
use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{

    public function index(Request $request)
    {
        return ExceptionHandler::handle(function () use ($request) {
            $limit = $request->per_page ?? 10;
            $messages = Schedule::orderBy('send_at', 'desc')->where('user_id', auth()->user()->id)->paginate($limit);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mendapatkan data Pesan',
                'data' => $messages,
            ]);
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:senders,id',
            'recipient' => 'required',
            'message' => 'required',
            'send_at' => 'required|date|after:now',
        ]);

        $schedule = Schedule::create([
            'user_id' => auth()->user()->id,
            'sender_id' => $request->sender_id,
            'recipient' => $request->recipient,
            'message' => $request->message,
            'send_at' => new Carbon($request->send_at),
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menyimpan data Pesan',
            'data' => $schedule,
        ]);
    }

    public function destroy(string $id)
    {
        return ExceptionHandler::handle(function () use ($id) {
            $msg = Schedule::findOrFail($id);
            $msg->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menghapus data Pesan',
            ]);
        });
    }

    public function runScheduler()
    {
        $now = now();

        $schedules = Schedule::with('sender')
            ->where('status', 'pending')
            ->where('send_at', '<=', $now)
            ->get();

        foreach ($schedules as $schedule) {
            $sender = $schedule->sender;

            $response = sendWhatsAppMessage($sender->token, $schedule->recipient, $schedule->message);

            if ($response['success']) {
                $schedule->status = 'sent';
            } else {
                $schedule->status = 'failed';
            }
            $schedule->save();
        }

        return response()->json(['message' => 'Scheduler run completed']);
    }
}
