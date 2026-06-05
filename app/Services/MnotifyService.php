<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MnotifyService
{
    protected string $apiKey;
    protected string $senderId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey   = config('services.mnotify.key');
        $this->senderId = config('services.mnotify.sender_id');
        $this->baseUrl  = config('services.mnotify.base_url');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $phone = $this->formatPhone($phone);

            $response = Http::get($this->baseUrl, [
                'key'       => $this->apiKey,
                'to'        => $phone,
                'msg'       => $message,
                'sender_id' => $this->senderId,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === 'success') {
                Log::info("SMS sent to {$phone}");
                return true;
            }

            Log::warning("SMS failed to {$phone}: " . json_encode($body));
            return false;

        } catch (\Exception $e) {
            Log::error("SMS error: " . $e->getMessage());
            return false;
        }
    }

    public function sendBulk(array $phones, string $message): bool
    {
        try {
            $formatted = collect($phones)
                ->map(fn($p) => $this->formatPhone($p))
                ->filter()
                ->join(',');

            $response = Http::get($this->baseUrl, [
                'key'       => $this->apiKey,
                'to'        => $formatted,
                'msg'       => $message,
                'sender_id' => $this->senderId,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("Bulk SMS error: " . $e->getMessage());
            return false;
        }
    }

    private function formatPhone(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // Convert 0244... to 233244...
        if (str_starts_with($phone, '0')) {
            $phone = '233' . substr($phone, 1);
        }

        // Add 233 if no country code
        if (!str_starts_with($phone, '233') && !str_starts_with($phone, '+')) {
            $phone = '233' . $phone;
        }

        return ltrim($phone, '+');
    }
}
