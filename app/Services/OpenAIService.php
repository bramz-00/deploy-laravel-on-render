<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function chat(array $messages, string $model = 'gpt-4o-mini', array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', array_merge([
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
            ], $options));

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('OpenAI API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateText(string $prompt, string $model = 'gpt-3.5-turbo'): string
    {
        $messages = [
            ['role' => 'user', 'content' => $prompt]
        ];

        $response = $this->chat($messages, $model);
        
        return $response['choices'][0]['message']['content'] ?? '';
    }

    public function generateImage(string $prompt, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/images/generations', array_merge([
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
            ], $options));

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('OpenAI Image API request failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('OpenAI Image API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}