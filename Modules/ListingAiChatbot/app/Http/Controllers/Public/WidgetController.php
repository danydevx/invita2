<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Modules\ListingAiChatbot\Models\ChatbotWidget;
use Illuminate\Http\Response;

class WidgetController extends Controller
{
    public function serveWidget(string $publicKey): Response
    {
        $widget = ChatbotWidget::wherePublicKey($publicKey)->first();

        if (!$widget || !$widget->is_enabled) {
            abort(404);
        }

        $jsPath = public_path('js/widget/widget.min.js');

        if (!file_exists($jsPath)) {
            abort(404);
        }

        $content = file_get_contents($jsPath);
        $etag = md5($content . $widget->version);

        return response($content, 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'public, max-age=86400, immutable',
            'Etag' => $etag,
            'X-Widget-Version' => $widget->version,
        ]);
    }
}
