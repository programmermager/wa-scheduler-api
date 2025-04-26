<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('sendWhatsAppMessage')) {
    function sendWhatsAppMessage($token, $recipient, $message,)
    {
        $url = 'https://api.fonnte.com/send';

        $payload = [
            'target' => $recipient,
            'message' => $message,
            'schedule' => 1,
            'countryCode' => '62'
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

if (!function_exists('getFonnteDevices')) {
    function getFonnteDevices($token)
    {

        $url = 'https://api.fonnte.com/get-devices';

        $response = Http::withToken($token)->get($url);

        return $response->json();
    }
}
