<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Promotions\Models\BusinessPromotion;

class PromotionVerificationController extends Controller
{
    public function verify(string $slug, int $promotionId, string $couponCode)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $promotion = BusinessPromotion::where('business_id', $business->id)
            ->where('id', $promotionId)
            ->first();

        if (!$promotion) {
            return $this->renderResult(false, 'Promoción no encontrada', $business, null);
        }

        if ($promotion->coupon_code !== $couponCode) {
            return $this->renderResult(false, 'Código de cupón incorrecto', $business, $promotion);
        }

        if (!$promotion->is_active) {
            return $this->renderResult(false, 'Esta promoción está desactivada', $business, $promotion);
        }

        if ($promotion->starts_at && now()->lt($promotion->starts_at)) {
            return $this->renderResult(false, 'Esta promoción aún no ha iniciado', $business, $promotion);
        }

        if ($promotion->expires_at && now()->gt($promotion->expires_at)) {
            return $this->renderResult(false, 'Esta promoción ha expirado', $business, $promotion);
        }

        return $this->renderResult(true, '¡Cupón válido!', $business, $promotion);
    }

    private function renderResult(bool $valid, string $message, Business $business, ?BusinessPromotion $promotion)
    {
        $promotionData = null;
        if ($promotion) {
            $promotionData = [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'description' => $promotion->description,
                'coupon_code' => $promotion->coupon_code,
                'regular_price' => $promotion->regular_price,
                'promotion_price' => $promotion->promotion_price,
                'discount' => $promotion->getDiscountPercentage(),
                'starts_at' => $promotion->starts_at?->format('d/m/Y'),
                'expires_at' => $promotion->expires_at?->format('d/m/Y'),
            ];
        }

        return Inertia::render('Public/Promotion/Verify', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo_path' => $business->logo_path,
            ],
            'valid' => $valid,
            'message' => $message,
            'promotion' => $promotionData,
        ]);
    }
}
