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

            // Kirim cURL ke API WhatsApp
            $response = $this->sendWhatsAppMessage($sender->token, $schedule->recipient, $schedule->message);

            // Update status
            if ($response['success']) {
                $schedule->status = 'sent';
            } else {
                $schedule->status = 'failed';
            }
            $schedule->save();
        }

        return response()->json(['message' => 'Scheduler run completed']);
    }

    private function sendWhatsAppMessage($token, $recipient, $message)
    {
        $url = 'https://your-whatsapp-api.com/send-message'; // Ganti dengan URL API WA kamu

        $payload = [
            'to' => $recipient,
            'message' => $message,
        ];

        $headers = [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'success' => $httpCode === 200,
            'response' => $result,
        ];
    }
}
