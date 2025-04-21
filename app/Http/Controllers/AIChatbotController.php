<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatbotController extends Controller
{
    private $openRouterApiKey;

    public function __construct()
    {
        $this->openRouterApiKey = env('OPENROUTER_API_KEY');

        if (empty($this->openRouterApiKey)) {
            Log::error('OpenRouter API key is not set in .env file');
        }
    }

    public function chat(Request $request)
    {
        try {
            $message = $request->input('message');

            if (empty($message)) {
                Log::warning('Empty message in request.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Message is required'
                ], 400);
            }

            Log::info('User message received', [
                'user_message' => $message
            ]);

            $aiResponse = $this->getAIResponse($message);

            return response()->json([
                'status' => 'success',
                'message' => $aiResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Error in chat()', [
                'exception_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }

    private function getAIResponse(string $message): string
    {
        if (empty($this->openRouterApiKey)) {
            Log::error('API key is empty, check .env');
            return 'Error: API key is not set.';
        }

        try {
            $payload = [
                'model' => 'deepseek/deepseek-chat-v3-0324:free',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a vacation assistant. Reply clearly and briefly.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ],
                ],
                'temperature' => 0.7
            ];

            Log::debug('Sending request to OpenRouter', [
                'payload' => $payload
            ]);

            $response = Http::withOptions([
                'verify' => 'C:\cacert\cacert.pem'
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->openRouterApiKey,
                    'HTTP-Referer' => 'http://localhost',
                    'Content-Type' => 'application/json'
                ])
                ->timeout(30)
                ->post('https://openrouter.ai/api/v1/chat/completions', $payload);

            Log::debug('Response from OpenRouter', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                return 'AI error: status ' . $response->status() . ' ' . $response->body();
            }

            $data = $response->json();

            if (!isset($data['choices'][0]['message']['content'])) {
                Log::warning('Unexpected OpenRouter response structure', [
                    'response' => $data
                ]);
                return 'Sorry, could not retrieve a valid response from the AI.';
            }

            return $data['choices'][0]['message']['content'];
        } catch (\Exception $e) {
            Log::error('Error in getAIResponse()', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'An error occurred while communicating with the AI.';
        }
    }
}
