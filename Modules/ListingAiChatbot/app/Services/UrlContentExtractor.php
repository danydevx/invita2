<?php

namespace Modules\ListingAiChatbot\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DOMDocument;

class UrlContentExtractor
{
    public function extract(string $url, int $maxChars = 5000): array
    {
        try {
            $response = Http::timeout(15)->get($url);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => 'No se pudo obtener el contenido de la URL. Código: ' . $response->status(),
                ];
            }

            $html = $response->body();

            $title = $this->extractTitle($html);
            $content = $this->extractText($html);
            $content = mb_substr($content, 0, $maxChars);

            return [
                'success' => true,
                'title' => $title,
                'content' => $content,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('UrlContentExtractor error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Error al extraer contenido: ' . $e->getMessage(),
            ];
        }
    }

    private function extractTitle(string $html): ?string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($html);
        libxml_clear_errors();

        $nodes = $doc->getElementsByTagName('title');

        if ($nodes->length > 0) {
            return trim($nodes->item(0)->textContent);
        }

        return null;
    }

    private function extractText(string $html): string
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();

        $doc->loadHTML(
            mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $this->removeScriptsAndStyles($doc);

        $body = $doc->getElementsByTagName('body')->item(0);

        if (!$body) {
            return strip_tags($html);
        }

        $text = $this->getTextContent($body);

        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);

        return $text;
    }

    private function removeScriptsAndStyles(DOMDocument $doc): void
    {
        $scripts = $doc->getElementsByTagName('script');
        $styles = $doc->getElementsByTagName('style');
        $navs = $doc->getElementsByTagName('nav');
        $footers = $doc->getElementsByTagName('footer');
        $headers = $doc->getElementsByTagName('header');

        foreach ([$scripts, $styles, $navs, $footers, $headers] as $elements) {
            $toRemove = [];
            foreach ($elements as $element) {
                $toRemove[] = $element;
            }
            foreach ($toRemove as $element) {
                $element->parentNode->removeChild($element);
            }
        }
    }

    private function getTextContent(\DOMNode $node): string
    {
        $text = '';

        foreach ($node->childNodes as $childNode) {
            if ($childNode instanceof \DOMText) {
                $text .= $childNode->wholeText . ' ';
            } elseif ($childNode instanceof \DOMElement) {
                $tagName = strtolower($childNode->tagName);

                if (in_array($tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'li', 'td', 'th', 'div', 'section', 'article'])) {
                    $text .= "\n" . $this->getTextContent($childNode) . "\n";
                } elseif ($tagName === 'br') {
                    $text .= "\n";
                } else {
                    $text .= $this->getTextContent($childNode);
                }
            }
        }

        return $text;
    }
}
