<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        $comments = $ticket->comments()->get();
        return response()->json($comments);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:255']
        ]);
        $comment = Comment::create([
            'description' => $request->comment,
            'user_id' => auth()->user()->id,
            'ticket_id' => $ticket->id
        ]);
        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment]);
    }

    public function show(Request $request, Ticket $ticket, Comment $comment)
    {
        return response()->json($comment);
    }

    public function update(Request $request, Ticket $ticket, Comment $comment)
    {
        $request->validate([
            'comment' => ['required', 'string', 'min:5', 'max:255']
        ]);
        $comment->update([
            'description' => $request->comment
        ]);
        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment]);
    }

    public function destroy(Request $request, Ticket $ticket, Comment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
