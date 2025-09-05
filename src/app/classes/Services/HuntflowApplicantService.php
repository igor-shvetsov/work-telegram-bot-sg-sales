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
        $this->baseUrl = 'https://api.huntflow.ru/v2';
        $this->apiToken = 'dc4a6421c41fad1814f680ddf10a10128826dac7d6864994d89e581f30a4bcb1';
        $this->accountId = '112319';

//        $this->baseUrl = config('services.huntflow.base_url');
//        $this->apiToken = config('services.huntflow.api_token');
//        $this->accountId = config('services.huntflow.account_id');
    }

    /**
     * Создание соискателя с полными данными
     */
    public function createApplicant(array $applicantData)
    {
        $url = "{$this->baseUrl}/accounts/{$this->accountId}/applicants";

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

    /**
     * Добавить отклик соискателя на вакансию
     *
     * @param int $applicantId ID созданного соискателя
     * @param int $vacancyId ID вакансии
     * @param int $statusId ID статуса воронки
     * @param array $additionalData Дополнительные данные
     * @return array|null
     */
    public function addApplicationToVacancy(
        int $applicantId,
        int $vacancyId,
        int $statusId,
        array $additionalData = []
    ) {
        $url = "{$this->baseUrl}/accounts/{$this->accountId}/applicants/{$applicantId}/vacancy";

        $data = array_merge([
            'vacancy' => $vacancyId,
            'status' => $statusId,
            'comment' => $additionalData['comment'] ?? 'Отклик с формы Tilda',
            // 'files' => $additionalData['files'] ?? [],
        ], $additionalData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)
                ->post($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'application_id' => $response->json()['id']
                ];
            }

            // Log::error('Huntflow API error: ' . $response->body());

            return [
                'success' => false,
                'error' => $response->body(),
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            // Log::error('Huntflow API error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getVacancyStatuses()
    {
        $url = "{$this->baseUrl}/accounts/{$this->accountId}/vacancies/statuses";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
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
}
