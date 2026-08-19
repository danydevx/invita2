<?php

namespace App\Services;

use App\Models\MessageTemplate;

class TemplateRenderService
{
    private array $cache = [];

    public function render(string $key, array $variables = [], ?string $fallback = null): ?string
    {
        $template = $this->resolveTemplate($key);
        if (! $template) {
            return $fallback;
        }

        return $this->renderText($template->content, $variables);
    }

    public function renderSubject(string $key, array $variables = [], ?string $fallback = null): ?string
    {
        $template = $this->resolveTemplate($key);
        if (! $template || $template->subject === null) {
            return $fallback;
        }

        return $this->renderText($template->subject, $variables);
    }

    public function renderLines(string $key, array $variables = []): ?array
    {
        $template = $this->resolveTemplate($key);
        if (! $template) {
            return null;
        }

        $content = $this->renderText($template->content, $variables);

        return preg_split("/\r\n|\r|\n/", (string) $content);
    }

    public function renderText(string $text, array $variables = []): string
    {
        // Reemplaza las variables con el formato {{variable}} por su valor en el arreglo.
        return (string) preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\s*}}/', function ($matches) use ($variables) {
            $key = $matches[1] ?? '';

            return (string) ($variables[$key] ?? '');
        }, $text);
    }

    private function resolveTemplate(string $key): ?MessageTemplate
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        // Guarda en cache el resultado para evitar consultas repetidas en la misma ejecucion.
        $template = MessageTemplate::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        $this->cache[$key] = $template;

        return $template;
    }
}
