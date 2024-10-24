<?php

namespace App\Http\Controllers\Frontend\Ticket\Comment;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function index(Ticket $ticket, Comment $comment)
    {
        $mediaFiles = $comment->getMedia('files');

        return response()->json([
            'media_files' => $mediaFiles,
        ]);
    }

    public function store(Ticket $ticket, Comment $comment, Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        try {
            $comment->addAllMediaFromRequest()->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('files');
            });
            return response()->json([
                'message' => 'Files uploaded successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload files.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Ticket $ticket, Comment $comment, Media $media)
    {
        if ($media->model_id !== $comment->id || $media->model_type !== Comment::class) {
            return response()->json([
                'message' => 'Media not found for this ticket.',
            ], 404);
        }
        return response()->json([
            'media_file' => $media,
        ]);
    }

//    public function update(Request $request, Media $media)
//    {
//    }

    public function destroy(Ticket $ticket, Comment $comment, Media $media)
    {
        if ($media->model_id !== $comment->id || $media->model_type !== Comment::class) {
            return response()->json([
                'message' => 'Media not found for this ticket.',
            ], 404);
        }
        $media->delete();
        return response()->json([
            'message' => 'File deleted successfully.',
        ], 200);
    }
}
