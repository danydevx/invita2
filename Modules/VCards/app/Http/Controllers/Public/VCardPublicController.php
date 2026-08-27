<?php

namespace Modules\VCards\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\VCards\Http\Resources\VCardResource;
use Modules\VCards\Models\VCard;

class VCardPublicController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $vcard = VCard::where('slug', $slug)
            ->where('active', true)
            ->where('paused', false)
            ->with(['contacts', 'activeFields', 'team', 'listing', 'listing.about', 'seoSetting', 'sections', 'selectedProducts', 'selectedProducts.product', 'selectedTestimonials', 'selectedTestimonials.review', 'businessHours', 'selectedMenuCategories.category', 'selectedMenuCategories.category.activeProducts', 'selectedLocation', 'selectedFeatures.feature'])
            ->firstOrFail();

        $vcard->incrementViews();

        $aiChatbot = $this->getAiChatbotSettings($vcard);

        return Inertia::render('Public/VCards/Show', [
            'vcard' => (new VCardResource($vcard))->resolve(request()),
            'aiChatbot' => $aiChatbot,
        ]);
    }

    private function getAiChatbotSettings(VCard $vcard): ?array
    {
        if (!$vcard->listing_id || !$vcard->ai_chat_enabled) {
            return null;
        }

        $aiSetting = \Modules\ListingAiChatbot\Models\ListingAiSetting::where('listing_id', $vcard->listing_id)
            ->where('is_enabled', true)
            ->first();

        if (!$aiSetting) {
            return null;
        }

        return [
            'is_enabled' => true,
            'chatbot_name' => $aiSetting->chatbot_name ?? 'Asistente Virtual',
            'chatbot_avatar' => $aiSetting->chatbot_avatar ?? '',
            'widget_color' => $aiSetting->widget_color ?? '#3B82F6',
            'widget_theme' => $aiSetting->widget_theme ?? 'light',
            'allow_reset_chat' => $aiSetting->allow_reset_chat ?? false,
        ];
    }

    public function qr(Request $request, string $slug)
    {
        $vcard = VCard::where('slug', $slug)
            ->where('active', true)
            ->where('paused', false)
            ->firstOrFail();

        $encoded = urlencode($vcard->public_url);
        $size = $request->get('size', 300);

        return redirect("https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encoded}");
    }

    public function download(Request $request, string $slug)
    {
        $vcard = VCard::where('slug', $slug)
            ->where('active', true)
            ->where('paused', false)
            ->with(['contacts', 'activeFields'])
            ->firstOrFail();

        $content = $vcard->toVCardString();
        $filename = Str::slug($vcard->name) . '.vcf';

        return response($content, 200, [
            'Content-Type' => 'text/vcard; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
