<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/webm|max:204800',
        ]);

        $disk = config('filesystems.default', 'public');
        $path = $request->file('file')->store('media', $disk);

        $media = Media::create([
            'title' => $request->title,
            'description' => $request->description,
            'disk' => $disk,
            'path' => $path,
            'media_type' => $request->file('file')->getMimeType(),
            'size' => $request->file('file')->getSize(),
        ]);

        return response()->json([
            'id' => $media->id,
            'title' => $media->title,
            'description' => $media->description,
            'media_type' => $media->media_type,
            'size' => $media->size,
            'public_url' => Storage::disk($disk)->url($media->path),
            'created_at' => $media->created_at->toISOString(),
        ], 201);
    }
}
