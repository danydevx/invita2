<?php

namespace Modules\ListingAiChatbot\Services;

use Modules\ListingAiChatbot\Models\AiEmbedding;
use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Illuminate\Support\Facades\Log;

class VectorStoreService
{
    private EmbeddingService $embeddingService;
    private ListingAiSetting $settings;

    public function __construct(ListingAiSetting $settings)
    {
        $this->settings = $settings;
        $this->embeddingService = new EmbeddingService(
            $settings->api_key,
            $settings->provider,
            $settings->embedding_model
        );
    }

    public function storeEmbedding(string $sourceType, int|string $sourceId, string $text): void
    {
        $existing = AiEmbedding::where('listing_id', $this->settings->listing_id)
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->first();

        if ($existing) {
            $this->updateEmbedding($existing, $text);
            return;
        }

        $this->createEmbedding($sourceType, $sourceId, $text);
    }

    public function deleteEmbedding(string $sourceType, int|string $sourceId): void
    {
        if (is_int($sourceId) || ctype_digit($sourceId)) {
            AiEmbedding::where('listing_id', $this->settings->listing_id)
                ->where('source_type', $sourceType)
                ->where(function ($q) use ($sourceId) {
                    $q->where('source_id', $sourceId)
                      ->orWhere('source_id', 'like', $sourceId . '_%');
                })
                ->delete();
        } else {
            AiEmbedding::where('listing_id', $this->settings->listing_id)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->delete();
        }
    }

    private const CHUNK_SIZE = 100;
    private const MAX_PROCESS = 500;

    public function searchSimilar(string $query, int $limit = 5, float $minSimilarity = 0.5): array
    {
        try {
            $queryEmbedding = $this->embeddingService->embed($query);
        } catch (\Exception $e) {
            Log::error('VectorStore search embedding error', [
                'listing_id' => $this->settings->listing_id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }

        $results = [];
        $processed = 0;

        AiEmbedding::where('listing_id', $this->settings->listing_id)
            ->select(['id', 'source_type', 'source_id', 'chunk_text', 'embedding'])
            ->chunk(self::CHUNK_SIZE, function ($embeddings) use ($queryEmbedding, $minSimilarity, &$results, &$processed) {
                foreach ($embeddings as $embedding) {
                    $processed++;
                    if ($processed > self::MAX_PROCESS) {
                        return false;
                    }

                    $storedEmbedding = $embedding->getEmbeddingArray();
                    if (empty($storedEmbedding)) {
                        continue;
                    }

                    $similarity = $this->embeddingService->cosineSimilarity($queryEmbedding, $storedEmbedding);

                    if ($similarity >= $minSimilarity) {
                        $results[] = [
                            'source_type' => $embedding->source_type,
                            'source_id' => $embedding->source_id,
                            'chunk_text' => $embedding->chunk_text,
                            'similarity' => round($similarity, 4),
                        ];
                    }
                }
                return true;
            });

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    public function searchSimilarInContexts(string $query, array $contextIds, int $limit = 5, float $minSimilarity = 0.5): array
    {
        if (empty($contextIds)) {
            return [];
        }

        try {
            $queryEmbedding = $this->embeddingService->embed($query);
        } catch (\Exception $e) {
            Log::error('VectorStore searchInContexts embedding error', [
                'listing_id' => $this->settings->listing_id,
                'context_ids' => $contextIds,
                'error' => $e->getMessage(),
            ]);
            return [];
        }

        $results = [];
        $processed = 0;

        AiEmbedding::where('listing_id', $this->settings->listing_id)
            ->where(function ($q) use ($contextIds) {
                foreach ($contextIds as $contextId) {
                    $parts = explode('_', $contextId);
                    if (count($parts) >= 2) {
                        $sourceType = $parts[0];
                        $sourceId = implode('_', array_slice($parts, 1));
                        $q->orWhere(function ($subQ) use ($sourceType, $sourceId) {
                            $subQ->where('source_type', $sourceType)
                                 ->where('source_id', $sourceId);
                        });
                    } else {
                        $q->orWhere(function ($subQ) use ($contextId) {
                            $subQ->where('source_type', 'custom')
                                 ->where('source_id', $contextId);
                        });
                    }
                }
            })
            ->select(['id', 'source_type', 'source_id', 'chunk_text', 'embedding'])
            ->chunk(self::CHUNK_SIZE, function ($embeddings) use ($queryEmbedding, $minSimilarity, &$results, &$processed) {
                foreach ($embeddings as $embedding) {
                    $processed++;
                    if ($processed > self::MAX_PROCESS) {
                        return false;
                    }

                    $storedEmbedding = $embedding->getEmbeddingArray();
                    if (empty($storedEmbedding)) {
                        continue;
                    }

                    $similarity = $this->embeddingService->cosineSimilarity($queryEmbedding, $storedEmbedding);

                    if ($similarity >= $minSimilarity) {
                        $results[] = [
                            'source_type' => $embedding->source_type,
                            'source_id' => $embedding->source_id,
                            'chunk_text' => $embedding->chunk_text,
                            'similarity' => round($similarity, 4),
                        ];
                    }
                }
                return true;
            });

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, $limit);
    }

    public function reindexBusiness(): array
    {
        $businessId = $this->settings->listing_id;

        AiEmbedding::where('listing_id', $businessId)->delete();

        $stats = [
            'products' => 0,
            'services' => 0,
            'promotions' => 0,
            'faqs' => 0,
            'locations' => 0,
            'about' => 0,
            'custom' => 0,
            'restaurant_menu' => 0,
            'social_networks' => 0,
            'appointments' => 0,
        ];

        $stats['products'] = $this->indexProducts($businessId);
        $stats['services'] = $this->indexServices($businessId);
        $stats['promotions'] = $this->indexPromotions($businessId);
        $stats['faqs'] = $this->indexFaqs($businessId);
        $stats['locations'] = $this->indexLocations($businessId);
        $stats['about'] = $this->indexAbout($businessId);
        $stats['custom'] = $this->indexCustomContexts($businessId);
        $stats['restaurant_menu'] = $this->indexRestaurantMenu($businessId);
        $stats['social_networks'] = $this->indexSocialNetworks($businessId);
        $stats['appointments'] = $this->indexAppointments($businessId);

        return $stats;
    }

    private function createEmbedding(string $sourceType, int|string $sourceId, string $text): void
    {
        if (!$this->isValidContent($text)) {
            Log::info('VectorStore skipped invalid content', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'reason' => 'invalid_content',
            ]);
            return;
        }

        $contentHash = $this->getContentHash($text);
        $existingWithHash = AiEmbedding::where('listing_id', $this->settings->listing_id)
            ->where('content_hash', $contentHash)
            ->first();

        if ($existingWithHash) {
            Log::info('VectorStore skipped duplicate content', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'existing_id' => $existingWithHash->id,
            ]);
            return;
        }

        try {
            $embeddingArray = $this->embeddingService->embed($text);

            AiEmbedding::create([
                'listing_id' => $this->settings->listing_id,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'chunk_text' => $text,
                'content_hash' => $contentHash,
                'embedding' => json_encode($embeddingArray),
            ]);
        } catch (\Exception $e) {
            Log::error('VectorStore create embedding error', [
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateEmbedding(AiEmbedding $embedding, string $text): void
    {
        if (!$this->isValidContent($text)) {
            $embedding->delete();
            Log::info('VectorStore deleted invalid content', [
                'id' => $embedding->id,
            ]);
            return;
        }

        $contentHash = $this->getContentHash($text);
        $existingWithHash = AiEmbedding::where('listing_id', $this->settings->listing_id)
            ->where('content_hash', $contentHash)
            ->where('id', '!=', $embedding->id)
            ->first();

        if ($existingWithHash) {
            $embedding->delete();
            Log::info('VectorStore merged duplicate content', [
                'deleted_id' => $embedding->id,
                'kept_id' => $existingWithHash->id,
            ]);
            return;
        }

        try {
            $embeddingArray = $this->embeddingService->embed($text);
            $embedding->update([
                'chunk_text' => $text,
                'content_hash' => $contentHash,
                'embedding' => json_encode($embeddingArray),
            ]);
        } catch (\Exception $e) {
            Log::error('VectorStore update embedding error', [
                'id' => $embedding->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function isValidContent(string $text): bool
    {
        if (empty(trim($text))) {
            return false;
        }

        $cleanText = trim($text);

        if (strlen($cleanText) < 10) {
            return false;
        }

        if (strlen($cleanText) > 10000) {
            return false;
        }

        $placeholderPatterns = [
            '/^null$/i',
            '/^undefined$/i',
            '/^\[object\]$/i',
            '/^undefined|^null|^false$/i',
            '/^(http|https):\/\//i',
            '/^<[^>]+>$/',
            '/^\s+$/',
            '/^[\d\W]+$/',
        ];

        foreach ($placeholderPatterns as $pattern) {
            if (preg_match($pattern, $cleanText)) {
                return false;
            }
        }

        if (preg_match('/^(.{0,5})\1{3,}$/u', $cleanText)) {
            return false;
        }

        $placeholderStrings = [
            'no disponible',
            'no especificado',
            'no definido',
            'por definir',
            'pending',
            'sin descripcion',
            'sin descripción',
            'sin informacion',
            'sin información',
        ];

        $lowerText = mb_strtolower($cleanText);
        foreach ($placeholderStrings as $placeholder) {
            if ($lowerText === mb_strtolower($placeholder)) {
                return false;
            }
        }

        return true;
    }

    private function getContentHash(string $text): string
    {
        $normalized = mb_strtolower(trim(preg_replace('/\s+/', ' ', $text)));
        return md5($normalized);
    }

    private function chunkText(string $text, int $maxLength = 500): array
    {
        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $chunks = [];
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        $currentChunk = '';
        foreach ($sentences as $sentence) {
            if (mb_strlen($currentChunk) + mb_strlen($sentence) <= $maxLength) {
                $currentChunk .= ($currentChunk ? ' ' : '') . $sentence;
            } else {
                if ($currentChunk) {
                    $chunks[] = trim($currentChunk);
                }
                $currentChunk = $sentence;
            }
        }

        if ($currentChunk) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    private function indexProducts(int $businessId): int
    {
        $products = \Modules\ListingProducts\Models\ListingProduct::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        $count = 0;
        foreach ($products as $product) {
            AiEmbedding::where('listing_id', $businessId)
                ->where('source_type', 'product')
                ->where('source_id', $product->id)
                ->delete();

            $baseText = implode('. ', array_filter([
                $product->name,
                $product->sku ? "SKU: {$product->sku}" : null,
                $product->price ? "Precio: {$product->price}" : null,
            ]));

            if ($product->description) {
                $chunks = $this->chunkText($product->description);
                $chunks[0] = $baseText . '. ' . $chunks[0];
                foreach ($chunks as $idx => $chunk) {
                    $this->createEmbedding('product', $product->id . '_' . $idx, $chunk);
                    $count++;
                }
            } elseif ($baseText) {
                $this->storeEmbedding('product', $product->id, $baseText);
                $count++;
            }
        }

        return $count;
    }

    private function indexServices(int $businessId): int
    {
        $services = \Modules\ListingServices\Models\ListingService::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        $count = 0;
        foreach ($services as $service) {
            AiEmbedding::where('listing_id', $businessId)
                ->where('source_type', 'service')
                ->where('source_id', $service->id)
                ->delete();

            $baseText = implode('. ', array_filter([
                $service->name,
                $service->duration_minutes ? "Duración: {$service->duration_minutes} minutos" : null,
                $service->price ? "Precio: {$service->price}" : null,
            ]));

            if ($service->description) {
                $chunks = $this->chunkText($service->description);
                $chunks[0] = $baseText . '. ' . $chunks[0];
                foreach ($chunks as $idx => $chunk) {
                    $this->createEmbedding('service', $service->id . '_' . $idx, $chunk);
                    $count++;
                }
            } elseif ($baseText) {
                $this->storeEmbedding('service', $service->id, $baseText);
                $count++;
            }
        }

        return $count;
    }

    private function indexPromotions(int $businessId): int
    {
        $promotions = \Modules\ListingPromotions\Models\ListingPromotion::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($promotions as $promo) {
            $text = implode('. ', array_filter([
                $promo->title,
                $promo->description,
                $promo->terms ? "Términos: {$promo->terms}" : null,
                $promo->discount_type === 'percentage'
                    ? "Descuento: {$promo->discount_value}%"
                    : ($promo->discount_value ? "Descuento: {$promo->discount_value}" : null),
            ]));

            if ($text) {
                $this->storeEmbedding('promotion', $promo->id, $text);
            }
        }

        return $promotions->count();
    }

    private function indexFaqs(int $businessId): int
    {
        $faqs = \Modules\ListingFaqs\Models\ListingFaq::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($faqs as $faq) {
            $text = "Pregunta: {$faq->question}. Respuesta: {$faq->answer}";

            $this->storeEmbedding('faq', $faq->id, $text);
        }

        return $faqs->count();
    }

    private function indexLocations(int $businessId): int
    {
        $locations = \Modules\ListingLocations\Models\ListingLocation::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($locations as $location) {
            $parts = array_filter([
                $location->name,
                $location->address_line_1,
                $location->address_line_2,
                $location->city,
                $location->municipality,
                $location->state ? "{$location->state} ({$location->state_code})" : null,
                $location->postal_code ? "CP {$location->postal_code}" : null,
                $location->country,
            ]);

            $address = implode(', ', $parts);

            $text = implode('. ', array_filter([
                $location->name,
                $address,
                $location->phone ? "Teléfono: {$location->phone}" : null,
                $location->email ? "Email: {$location->email}" : null,
                $location->directions_url ? "Google Maps: {$location->directions_url}" : null,
            ]));

            if ($text) {
                $this->storeEmbedding('location', $location->id, $text);
            }
        }

        return $locations->count();
    }

    private function indexAbout(int $businessId): int
    {
        $abouts = \Modules\ListingAbout\Models\ListingAbout::where('listing_id', $businessId)
            ->get();

        foreach ($abouts as $about) {
            $text = "Acerca de: {$about->content}";

            $this->storeEmbedding('about', $about->id, $text);
        }

        return $abouts->count();
    }

    private function indexCustomContexts(int $businessId): int
    {
        $contexts = \Modules\ListingAiChatbot\Models\AiContext::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($contexts as $context) {
            $text = "{$context->title}. {$context->content}";

            $this->storeEmbedding('custom', $context->id, $text);
        }

        return $contexts->count();
    }

    private function indexRestaurantMenu(int $businessId): int
    {
        $count = 0;

        $categories = \Modules\ListingRestaurantMenu\Entities\MenuCategory::where('listing_id', $businessId)
            ->where('active', true)
            ->with(['activeProducts.variants', 'children.activeProducts.variants'])
            ->whereNull('parent_id')
            ->get();

        foreach ($categories as $category) {
            $categoryText = "Categoría del menú: {$category->title}";
            if ($category->description) {
                $categoryText .= ". {$category->description}";
            }
            $this->storeEmbedding('restaurant_category', $category->id, $categoryText);
            $count++;

            foreach ($category->activeProducts as $product) {
                $productText = "Producto del menú: {$product->title}";
                if ($product->description) {
                    $productText .= ". {$product->description}";
                }
                if ($product->base_price) {
                    $productText .= ". Precio: {$product->base_price}";
                }

                $variantTexts = [];
                foreach ($product->activeVariants as $variant) {
                    $variantText = "{$variant->title}";
                    if ($variant->description) {
                        $variantText .= " - {$variant->description}";
                    }
                    if ($variant->price) {
                        $variantText .= " - Precio: {$variant->price}";
                    }
                    $variantTexts[] = $variantText;
                }
                if ($variantTexts) {
                    $productText .= ". Variantes: " . implode('. ', $variantTexts);
                }

                $this->storeEmbedding('restaurant_product', $product->id, $productText);
                $count++;
            }

            foreach ($category->children as $child) {
                $childText = "Subcategoría del menú: {$child->title}";
                if ($child->description) {
                    $childText .= ". {$child->description}";
                }
                $this->storeEmbedding('restaurant_category', $child->id, $childText);
                $count++;

                foreach ($child->activeProducts as $product) {
                    $productText = "Producto del menú: {$product->title}";
                    if ($product->description) {
                        $productText .= ". {$product->description}";
                    }
                    if ($product->base_price) {
                        $productText .= ". Precio: {$product->base_price}";
                    }

                    $variantTexts = [];
                    foreach ($product->activeVariants as $variant) {
                        $variantText = "{$variant->title}";
                        if ($variant->description) {
                            $variantText .= " - {$variant->description}";
                        }
                        if ($variant->price) {
                            $variantText .= " - Precio: {$variant->price}";
                        }
                        $variantTexts[] = $variantText;
                    }
                    if ($variantTexts) {
                        $productText .= ". Variantes: " . implode('. ', $variantTexts);
                    }

                    $this->storeEmbedding('restaurant_product', $product->id, $productText);
                    $count++;
                }
            }
        }

        return $count;
    }

    private function indexSocialNetworks(int $businessId): int
    {
        if (!class_exists('\Modules\ListingSocialMedia\Models\ListingSocialNetwork')) {
            return 0;
        }

        $socialNetworks = \Modules\ListingSocialMedia\Models\ListingSocialNetwork::where('listing_id', $businessId)
            ->where('is_active', true)
            ->get();

        foreach ($socialNetworks as $sn) {
            $text = implode('. ', array_filter([
                "Red social: {$sn->name}",
                $sn->username ? "Usuario: {$sn->username}" : null,
                $sn->url ? "URL: {$sn->url}" : null,
            ]));

            if ($text) {
                $this->storeEmbedding('social_network', $sn->id, $text);
            }
        }

        return $socialNetworks->count();
    }

    private function indexAppointments(int $businessId): int
    {
        if (!class_exists('\Modules\ListingAppointments\Models\ListingAvailability')) {
            return 0;
        }

        $count = 0;

        $availabilities = \Modules\ListingAppointments\Models\ListingAvailability::where('listing_id', $businessId)
            ->orderBy('day_of_week')
            ->get();

        $scheduleParts = [];
        foreach ($availabilities as $avail) {
            $dayName = \Modules\ListingAppointments\Models\ListingAvailability::dayName($avail->day_of_week);
            if ($avail->is_available) {
                $scheduleParts[] = "{$dayName}: de {$avail->start_time} a {$avail->end_time}, duración de cita {$avail->slot_duration_minutes} minutos";
            } else {
                $scheduleParts[] = "{$dayName}: cerrado";
            }
        }

        if (!empty($scheduleParts)) {
            $text = "Horarios de atención: " . implode('. ', $scheduleParts) . ".";
            $this->storeEmbedding('appointment', $businessId, $text);
            $count++;
        }

        $exceptions = \Modules\ListingAppointments\Models\ListingAvailabilityException::where('listing_id', $businessId)
            ->where('exception_date', '>=', now()->toDateString())
            ->orderBy('exception_date')
            ->limit(30)
            ->get();

        foreach ($exceptions as $exception) {
            $date = $exception->exception_date->format('d/m/Y');
            if ($exception->is_available) {
                $text = "Día especial: {$date} - Horario especial: {$exception->start_time} a {$exception->end_time}. Razón: {$exception->reason}";
            } else {
                $text = "Día cerrado: {$date}. Razón: {$exception->reason}";
            }
            $this->storeEmbedding('appointment_exception', $exception->id, $text);
            $count++;
        }

        return $count;
    }
}
