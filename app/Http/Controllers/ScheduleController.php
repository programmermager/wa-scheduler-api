<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:senders,id',
            'recipient' => 'required',
            'message' => 'required',
            'send_at' => 'required|date|after:now',
        ]);

        $schedule = Schedule::create([
            'user_id' => $request->user()->id,
            'sender_id' => $request->sender_id,
            'recipient' => $request->recipient,
            'message' => $request->message,
            'send_at' => new Carbon($request->send_at),
            'status' => 'pending',
        ]);

        return response()->json($schedule);
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
