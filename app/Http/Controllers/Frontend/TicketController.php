<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user_tickets = auth()->user()->tickets()->with(['comments', 'assigned_agent', 'category', 'files'])->get();
        return response()->json($user_tickets);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:1', 'max:255'],
            'priority' => ['required', 'integer', 'between:0,2'],
            'category_id' => ['required', 'exists:categories,id']
        ]);
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 0,//0:open, 1:in progress, 2:closed
            'priority' => $request->priority,//0:low, 1:medium, 2:high
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id
        ]);
        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket]);
    }

    public function post_comment(Request $request, Ticket $ticket)
    {
        $this->authorize('post_comment', $ticket);
        $request->validate([
            'comment' => 'required|string|min:5,max:255'
        ]);
        $ticket->comments()->create([
            'description' => $request->comment,
            'user_id' => auth()->user()->id
        ]);
        return response()->json(['message' => 'Comment posted successfully', 'comment' => $request->comment]);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket->load(['comments', 'assigned_agent', 'category', 'files']);
        return response()->json($ticket);
    }

    public function closed_tickets()
    {
        return view('frontend.ticket.closed');
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:1', 'max:255'],
            'priority' => ['required', 'integer', 'between:0,2'], //0:low, 1:medium, 2:high
            'category_id' => ['required', 'exists:categories,id']
        ]);
        try {
            $ticket = Ticket::findOrFail($id);
            if ($ticket->status === 2) {
                return response()->json(['error' => 'Ticket is closed'], 400);
            } else if ($ticket->status === 1) {
                return response()->json(['error' => 'Ticket is in progress'], 400);
            }
            $ticket->update([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'category_id' => $request->category_id
            ]);
            return response()->json(['message' => 'Ticket updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrfail($id);
        $this->authorize('delete', $ticket);
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
