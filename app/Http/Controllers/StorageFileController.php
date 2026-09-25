<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageFileController extends Controller
{
    /**
     * Layani file dari storage publik secara otomatis jika symlink tidak aktif / diblokir hosting
     */
    public function showStorage(string $path)
    {
        // Sanitasi path untuk mencegah directory traversal
        $path = str_replace(['..', "\0"], '', $path);
        $path = ltrim($path, '/');

        $candidates = [
            storage_path('app/public/' . $path),
            public_path('storage/' . $path),
            public_path('uploads/' . $path),
            storage_path('app/public/uploads/' . $path),
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && !File::isDirectory($candidate)) {
                return $this->createFileResponse($candidate);
            }
        }

        abort(404, 'File storage tidak ditemukan.');
    }

    /**
     * Layani file dari uploads (logo, hero thumbnail, dll) jika public_path terpisah dari public_html
     */
    public function showUploads(string $path)
    {
        // Sanitasi path untuk mencegah directory traversal
        $path = str_replace(['..', "\0"], '', $path);
        $path = ltrim($path, '/');

        $candidates = [
            public_path('uploads/' . $path),
            storage_path('app/public/uploads/' . $path),
            storage_path('app/public/' . $path),
            public_path('storage/uploads/' . $path),
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && !File::isDirectory($candidate)) {
                return $this->createFileResponse($candidate);
            }
        }

        abort(404, 'File upload tidak ditemukan.');
    }

    /**
     * Buat response file dengan header caching optimal untuk performa
     */
    protected function createFileResponse(string $fullPath): BinaryFileResponse
    {
        $mime = File::mimeType($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type'        => $mime,
            'Cache-Control'       => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
