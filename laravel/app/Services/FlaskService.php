<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FlaskService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.flask.url', 'http://localhost:5000');
        $this->apiKey  = config('services.flask.api_key', '');
        $this->timeout = config('services.flask.timeout', 15);
    }

    /**
     * Kirim gambar ke Flask untuk face recognition
     */
    public function recognize(string $imageBase64): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/recognize", [
                'image' => $imageBase64,
            ]);

            if ($response->serverError()) {
                Log::error('Flask recognize server error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new Exception('AI engine mengalami error internal.');
            }

            $data = $response->json();

            if ($data === null) {
                throw new Exception('Response dari AI engine tidak valid.');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::critical('Flask service is down (connection refused)', [
                'url'   => $this->baseUrl,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('AI engine tidak dapat dijangkau. Pastikan Flask service berjalan di port 5000.');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Flask request exception', ['error' => $e->getMessage()]);
            throw new Exception('Gagal berkomunikasi dengan AI engine: ' . $e->getMessage());
        }
    }

    /**
     * Daftarkan wajah mahasiswa ke Flask untuk extract embedding
     */
    public function registerFace(int $studentId, string $imageBase64): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/register-face", [
                'student_id' => $studentId,
                'image'      => $imageBase64,
            ]);

            $data = $response->json();

            if ($data === null) {
                throw new Exception('Response tidak valid dari AI engine.');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::critical('Flask down on registerFace', ['error' => $e->getMessage()]);
            throw new Exception('AI engine tidak tersedia untuk registrasi wajah.');
        }
    }

    /**
     * Health check — return true jika Flask online
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->timeout(5)
                ->get("{$this->baseUrl}/health");

            return $response->successful() &&
                   (($response->json('status') ?? '') === 'ok');

        } catch (Exception $e) {
            Log::debug('Flask health check failed: ' . $e->getMessage());
            return false;
        }
    }
}
