<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmsApiService
{
    protected string $baseUrl;
    protected string $serviceKey;

    public function __construct()
    {
        $this->baseUrl    = config('services.ems.api_url');
        $this->serviceKey = config('services.ems.service_key');
    }

    //  Get all user list
    public function getUsers(): array
    {
        $response = $this->request()->get("{$this->baseUrl}/users");

        if ($response->failed()) {
            Log::error('EMS API: Failed to fetch users', [
                'status' => $response->status(),
            ]);
            throw new \Exception('Unable to fetch users from EMS.');
        }

        return $response->json('users', []);
    }

    //  Get detail of a specific user
    public function getUserDetail(int $userId): array
    {
        $response = $this->request()->get("{$this->baseUrl}/users/{$userId}");

        if ($response->status() === 404) {
            throw new \Exception('User not found.');
        }

        if ($response->failed()) {
            Log::error('EMS API: Failed to fetch user detail', [
                'user_id' => $userId,
                'status'  => $response->status(),
            ]);
            throw new \Exception('Unable to fetch user detail from EMS.');
        }

        return $response->json('user', []);
    }

    // Common Request Builder 
    protected function request()
    {
        return Http::withHeaders([
            'X-Service-Key' => $this->serviceKey,
            'Accept'        => 'application/json',
        ])->timeout(10);
    }
}