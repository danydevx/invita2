<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiTestController extends Controller
{
    public function test()
    {
        $apiKey = config('ai.providers.openai.key');

        if (empty($apiKey) || $apiKey === 'your-openai-api-key-here') {
            return response()->json([
                'error' => 'OpenAI API key not configured',
                'message' => 'Please add your OPENAI_API_KEY to the .env file',
            ], 400);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant.',
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Say "Hello from Laravel AI!" and nothing else.',
                        ],
                    ],
                    'max_tokens' => 50,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => $data['choices'][0]['message']['content'] ?? 'No response content',
                    'model' => $data['model'] ?? 'unknown',
                    'usage' => $data['usage'] ?? null,
                ]);
            }

            return response()->json([
                'error' => 'OpenAI API error',
                'status' => $response->status(),
                'message' => $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function chat(Request $request)
    {
        $apiKey = config('ai.providers.openai.key');

        if (empty($apiKey) || $apiKey === 'your-openai-api-key-here') {
            return response()->json([
                'error' => 'OpenAI API key not configured',
            ], 400);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant for a business called "Mi Negocio". Keep responses concise and friendly.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->message,
                        ],
                    ],
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'reply' => $data['choices'][0]['message']['content'] ?? 'No response',
                    'usage' => $data['usage'] ?? null,
                ]);
            }

            return response()->json([
                'error' => 'OpenAI API error',
                'message' => $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
