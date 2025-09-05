<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuntflowApplicantService
{
    protected $baseUrl;
    protected $apiToken;
    protected $accountId;

    public function __construct()
    {
        $this->baseUrl = config('services.huntflow.base_url');
        $this->apiToken = config('services.huntflow.api_token');
        $this->accountId = config('services.huntflow.account_id');
    }

    /**
     * Создание соискателя с полными данными
     */
    public function createApplicant(array $applicantData)
    {
        $url = "{$this->baseUrl}/account/{$this->accountId}/applicants";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)
                ->post($url, $applicantData);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'applicant_id' => $response->json()['id']
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Huntflow API error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Загрузка файла (резюме)
     */
    public function uploadResume($file, $parseText = true)
    {
        $url = "{$this->baseUrl}/account/{$this->accountId}/upload";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'X-File-Parse' => $parseText ? 'true' : 'false',
            ])->attach('file', file_get_contents($file->path()), $file->getClientOriginalName())
                ->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'file_id' => $response->json()['id'],
                    'file_data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Huntflow upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
