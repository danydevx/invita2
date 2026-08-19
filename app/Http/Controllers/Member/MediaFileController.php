<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Services\AccessService;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaFileController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
    ];

    public function index(Request $request)
    {
        $files = MediaFile::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($file) => [
                'id' => $file->id,
                'original_name' => $file->original_name,
                'type' => $file->type,
                'mime_type' => $file->mime_type,
                'extension' => $file->extension,
                'size' => $file->size,
                'created_at' => $file->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Member/Files/Index', [
            'files' => $files,
            'maxSizeKb' => self::MAX_FILE_SIZE_KB,
            'allowedTypes' => self::ALLOWED_MIME_TYPES,
        ]);
    }

    public function store(Request $request, AccessService $access, ActivityService $activity)
    {
        if (! $access->canUploadFiles($request->user())) {
            return back()->withErrors(['file' => 'No tienes permitido subir archivos.']);
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'max:'.self::MAX_FILE_SIZE_KB, 'mimetypes:'.implode(',', self::ALLOWED_MIME_TYPES)],
        ], [
            'file.max' => 'El archivo supera el tamaño permitido.',
            'file.mimetypes' => 'El tipo de archivo no está permitido.',
        ]);

        $file = $data['file'];
        $disk = config('filesystems.default', 'local');
        $path = $file->store('media/'.$request->user()->id, ['disk' => $disk, 'visibility' => 'private']);

        $media = MediaFile::create([
            'user_id' => $request->user()->id,
            'disk' => $disk,
            'path' => $path,
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension() ?: null,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'type' => $this->inferType($file->getClientMimeType()),
            'visibility' => 'private',
        ]);

        $activity->log('file_uploaded', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $media,
            'description' => 'Archivo subido',
            'request' => $request,
        ]);

        return back()->with('success', 'Archivo subido correctamente.');
    }

    public function show(Request $request, MediaFile $file)
    {
        $this->authorize('view', $file);

        return Inertia::render('Member/Files/Show', [
            'file' => [
                'id' => $file->id,
                'original_name' => $file->original_name,
                'type' => $file->type,
                'mime_type' => $file->mime_type,
                'extension' => $file->extension,
                'size' => $file->size,
                'created_at' => $file->created_at?->toDateTimeString(),
            ],
        ]);
    }

    public function download(Request $request, MediaFile $file, ActivityService $activity)
    {
        $this->authorize('download', $file);

        if (! Storage::disk($file->disk)->exists($file->path)) {
            return back()->with('error', 'El archivo no está disponible.');
        }

        $activity->log('file_downloaded', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $file,
            'description' => 'Archivo descargado',
            'request' => $request,
        ]);

        return Storage::disk($file->disk)->download($file->path, $file->original_name);
    }

    public function destroy(Request $request, MediaFile $file, ActivityService $activity)
    {
        $this->authorize('delete', $file);

        if (Storage::disk($file->disk)->exists($file->path)) {
            Storage::disk($file->disk)->delete($file->path);
        }

        $file->delete();

        $activity->log('file_deleted', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $file,
            'description' => 'Archivo eliminado',
            'request' => $request,
        ]);

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    private function inferType(?string $mime): string
    {
        if (! $mime) {
            return 'other';
        }

        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }

        if (str_contains($mime, 'pdf') || str_contains($mime, 'word') || $mime === 'text/plain') {
            return 'document';
        }

        return 'other';
    }
}
