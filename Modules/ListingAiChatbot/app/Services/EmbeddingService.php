<?php

namespace Modules\ListingAiChatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private string $apiKey;
    private string $provider;
    private string $model;

    public function __construct(string $apiKey, string $provider = 'openai', string $model = 'text-embedding-3-small')
    {
        $this->apiKey = $apiKey;
        $this->provider = $provider;
        $this->model = $model;
    }

    public function embed(string $text): array
    {
        if ($this->provider === 'openai') {
            return $this->openAiEmbed($text);
        }

        if ($this->provider === 'minimax') {
            return $this->minimaxEmbed($text);
        }

        throw new \Exception("Provider {$this->provider} not supported");
    }

    public function embedBatch(array $texts): array
    {
        if ($this->provider === 'openai') {
            return $this->openAiEmbedBatch($texts);
        }

        if ($this->provider === 'minimax') {
            return $this->minimaxEmbedBatch($texts);
        }

        throw new \Exception("Provider {$this->provider} not supported");
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $magnitudeA = 0.0;
        $magnitudeB = 0.0;

        for ($i = 0; $i < count($a); $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $magnitudeA += $a[$i] * $a[$i];
            $magnitudeB += $b[$i] * $b[$i];
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA === 0.0 || $magnitudeB === 0.0) {
            return 0.0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    private function openAiEmbed(string $text): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(60)
            ->post('https://api.openai.com/v1/embeddings', [
                'input' => $text,
                'model' => $this->model,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI Embedding error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to generate embedding: ' . $response->body());
        }

        $data = $response->json();

        return $data['data'][0]['embedding'] ?? [];
    }

    private function openAiEmbedBatch(array $texts): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(120)
            ->post('https://api.openai.com/v1/embeddings', [
                'input' => $texts,
                'model' => $this->model,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI Embedding batch error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to generate embeddings: ' . $response->body());
        }

        $data = $response->json();

        return array_map(fn($item) => $item['embedding'], $data['data']);
    }

    private function minimaxEmbed(string $text): array
    {
        throw new \Exception('MiniMax embedding not implemented yet');
    }

    private function minimaxEmbedBatch(array $texts): array
    {
        throw new \Exception('MiniMax embedding not implemented yet');
    }
}
