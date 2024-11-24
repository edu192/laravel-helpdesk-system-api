<?php

namespace App\Http\Controllers\Frontend\Ticket;

use App\Events\TicketStatusChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedFilters([
                'id',
                'title',
                'description',
                'status',
                'priority',
                'user_id',
                'category_id',
            ])->allowedIncludes(['comments', 'assigned_agent', 'category', 'files'])
            ->where('user_id', auth()->user()->id)
            ->get();
        return response()->json($tickets);
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
        event(new TicketStatusChangedEvent($ticket));
        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket]);
    }


    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        $ticket = QueryBuilder::for(Ticket::class)
            ->allowedIncludes(['comments', 'assigned_agent', 'category', 'files'])
            ->where('user_id', auth()->user()->id)
            ->where('id', $ticket->id)
            ->first();
        return response()->json($ticket);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['required', 'string', 'min:1', 'max:255'],
            'priority' => ['required', 'integer', 'between:0,2'], //0:low, 1:medium, 2:high
            'category_id' => ['required', 'exists:categories,id']
        ]);
        $this->authorize('update', $ticket);
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
        return response()->json(['message' => 'Ticket updated successfully', 'ticket' => $ticket]);
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        if ($ticket->status === 2) {
            return response()->json(['error' => 'Ticket is closed'], 400);
        } else if ($ticket->status === 1) {
            return response()->json(['error' => 'Ticket is in progress'], 400);
        }
        $this->authorize('delete', $ticket);
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
