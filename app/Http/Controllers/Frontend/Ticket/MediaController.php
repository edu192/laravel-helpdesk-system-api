<?php

namespace App\Http\Controllers\Frontend\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function index(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to view files for this ticket.',
            ], 403);
        }

        $mediaFiles = $ticket->getMedia('files');

        return response()->json([
            'media_files' => $mediaFiles,
        ]);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        if ($ticket->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not allowed to upload files for this ticket.',
            ], 403);
        }
        try {
            $ticket->addAllMediaFromRequest()->each(function ($fileAdder) {
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

    public function show(Request $request, Ticket $ticket, Media $media)
    {
        if (Gate::denies('media_model_type_ownership', $media)) {
            return response()->json([
                'message' => 'You are not allowed to view files for this ticket.',
            ], 403);
        }
        if ($media->model_id !== $ticket->id || $media->model_type !== Ticket::class) {
            return response()->json([
                'message' => 'Media not found for this ticket.',
            ], 404);
        }
        return $media->toResponse($request);
    }

    public function update(Request $request, Media $media)
    {
    }

    public function destroy(Ticket $ticket, Media $media)
    {
        if (Gate::denies('media_model_type_ownership', $media)) {
            return response()->json([
                'message' => 'You are not allowed to delete files for this ticket.',
            ], 403);
        }
        if ($media->model_id !== $ticket->id || $media->model_type !== Ticket::class) {
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
