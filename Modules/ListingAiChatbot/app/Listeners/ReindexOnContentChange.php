<?php

namespace Modules\ListingAiChatbot\Listeners;

use Modules\ListingAiChatbot\Events\BusinessContentChanged;
use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Services\VectorStoreService;
use Illuminate\Support\Facades\Log;

class ReindexOnContentChange
{
    public function handle(BusinessContentChanged $event): void
    {
        $settings = ListingAiSetting::where('listing_id', $event->businessId)->first();

        if (!$settings || !$settings->is_enabled) {
            return;
        }

        try {
            $vectorStore = new VectorStoreService($settings);

            if ($event->action === 'deleted') {
                $vectorStore->deleteEmbedding($event->sourceType, $event->sourceId);
                return;
            }

            $text = $this->getContentText($event->sourceType, $event->sourceId);

            if ($text) {
                $vectorStore->storeEmbedding($event->sourceType, $event->sourceId, $text);
            }
        } catch (\Exception $e) {
            Log::error('Auto-reindex failed', [
                'listing_id' => $event->businessId,
                'source_type' => $event->sourceType,
                'source_id' => $event->sourceId,
                'action' => $event->action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function getContentText(string $sourceType, int $sourceId): ?string
    {
        return match ($sourceType) {
            'product' => $this->getProductText($sourceId),
            'service' => $this->getServiceText($sourceId),
            'promotion' => $this->getPromotionText($sourceId),
            'faq' => $this->getFaqText($sourceId),
            'location' => $this->getLocationText($sourceId),
            'about' => $this->getAboutText($sourceId),
            'social_network' => $this->getSocialNetworkText($sourceId),
            'restaurant_category' => $this->getRestaurantCategoryText($sourceId),
            'restaurant_product' => $this->getRestaurantProductText($sourceId),
            'appointment' => null,
            'appointment_exception' => null,
            default => null,
        };
    }

    private function getProductText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingProducts\Models\ListingProduct')) {
            return null;
        }
        $product = \Modules\ListingProducts\Models\ListingProduct::find($id);
        if (!$product) return null;

        return implode('. ', array_filter([
            $product->name,
            $product->description,
            $product->sku ? "SKU: {$product->sku}" : null,
            $product->price ? "Precio: {$product->price}" : null,
        ]));
    }

    private function getServiceText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingServices\Models\ListingService')) {
            return null;
        }
        $service = \Modules\ListingServices\Models\ListingService::find($id);
        if (!$service) return null;

        return implode('. ', array_filter([
            $service->name,
            $service->description,
            $service->duration_minutes ? "Duración: {$service->duration_minutes} minutos" : null,
            $service->price ? "Precio: {$service->price}" : null,
        ]));
    }

    private function getPromotionText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingPromotions\Models\ListingPromotion')) {
            return null;
        }
        $promo = \Modules\ListingPromotions\Models\ListingPromotion::find($id);
        if (!$promo) return null;

        return implode('. ', array_filter([
            $promo->title,
            $promo->description,
            $promo->terms ? "Términos: {$promo->terms}" : null,
            $promo->discount_type === 'percentage'
                ? "Descuento: {$promo->discount_value}%"
                : ($promo->discount_value ? "Descuento: {$promo->discount_value}" : null),
        ]));
    }

    private function getFaqText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingFaqs\Models\ListingFaq')) {
            return null;
        }
        $faq = \Modules\ListingFaqs\Models\ListingFaq::find($id);
        if (!$faq) return null;

        return "Pregunta: {$faq->question}. Respuesta: {$faq->answer}";
    }

    private function getLocationText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingLocations\Models\ListingLocation')) {
            return null;
        }
        $location = \Modules\ListingLocations\Models\ListingLocation::find($id);
        if (!$location) return null;

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

        return implode('. ', array_filter([
            $location->name,
            $address,
            $location->phone ? "Teléfono: {$location->phone}" : null,
            $location->email ? "Email: {$location->email}" : null,
            $location->directions_url ? "Google Maps: {$location->directions_url}" : null,
        ]));
    }

    private function getAboutText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingAbout\Models\ListingAbout')) {
            return null;
        }
        $about = \Modules\ListingAbout\Models\ListingAbout::find($id);
        if (!$about) return null;

        return "Acerca de: {$about->content}";
    }

    private function getSocialNetworkText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingSocialMedia\Models\ListingSocialNetwork')) {
            return null;
        }
        $sn = \Modules\ListingSocialMedia\Models\ListingSocialNetwork::find($id);
        if (!$sn) return null;

        return implode('. ', array_filter([
            "Red social: {$sn->name}",
            $sn->username ? "Usuario: {$sn->username}" : null,
            $sn->url ? "URL: {$sn->url}" : null,
        ]));
    }

    private function getRestaurantCategoryText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingRestaurantMenu\Entities\MenuCategory')) {
            return null;
        }
        $category = \Modules\ListingRestaurantMenu\Entities\MenuCategory::find($id);
        if (!$category) return null;

        $text = "Categoría del menú: {$category->title}";
        if ($category->description) {
            $text .= ". {$category->description}";
        }
        return $text;
    }

    private function getRestaurantProductText(int $id): ?string
    {
        if (!class_exists('\Modules\ListingRestaurantMenu\Entities\MenuProduct')) {
            return null;
        }
        $product = \Modules\ListingRestaurantMenu\Entities\MenuProduct::find($id);
        if (!$product) return null;

        $text = "Producto del menú: {$product->title}";
        if ($product->description) {
            $text .= ". {$product->description}";
        }
        if ($product->base_price) {
            $text .= ". Precio: {$product->base_price}";
        }
        return $text;
    }
}
