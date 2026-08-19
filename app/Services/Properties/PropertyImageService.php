<?php

namespace App\Services\Properties;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Properties\Models\Property;

class PropertyImageService
{
    protected const DISK = 'public';
    protected const DIRECTORY = 'properties';

    public function saveMainImage(Property $property, UploadedFile $file): string
    {
        if ($property->main_image) {
            $this->deleteMainImage($property);
        }

        $filename = $this->generateFilename($file);
        $path = $file->storeAs(self::DIRECTORY, $filename, self::DISK);

        $property->update(['main_image' => $path]);

        return $path;
    }

    public function deleteMainImage(Property $property): void
    {
        if ($property->main_image) {
            Storage::disk(self::DISK)->delete($property->main_image);
            $property->update(['main_image' => null]);
        }
    }

    public function getImageUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        if (str_starts_with($path, '/tmp/') || str_starts_with($path, 'tmp/')) {
            return '';
        }

        return "/storage/{$path}";
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $timestamp = now()->format('YmdHis');
        $random = \Illuminate\Support\Str::random(8);
        $extension = $file->getClientOriginalExtension() ?? 'jpg';

        return "property_{$timestamp}_{$random}.{$extension}";
    }

    public function validateImage(UploadedFile $file): array
    {
        $errors = [];
        $maxSize = 5 * 1024; // 5MB in KB
        $allowedMimes = ['jpeg', 'jpg', 'png', 'webp'];

        if ($file->getSize() > $maxSize * 1024) {
            $errors[] = "La imagen no puede exceder {$maxSize}KB.";
        }

        $mime = strtolower($file->getClientOriginalExtension() ?? '');
        if (! in_array($mime, $allowedMimes)) {
            $errors[] = 'La imagen debe ser JPG, PNG o WebP.';
        }

        return $errors;
    }
}
