<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TicketController extends Controller
{
//    public function index()
//    {
//        return view('backend.ticket.index');
//    }
//
//    public function assigned_tickets()
//    {
//        return view('backend.ticket.agent.index');
//    }
//
//    public function view(Ticket $ticket)
//    {
//        return view('backend.ticket.comments', compact('ticket'));
//    }
//    public function post_comment(Request $request, Ticket $ticket)
//    {
//        $this->authorize('post_comment', $ticket);
//        $request->validate([
//            'comment' => 'required|string|min:5,max:255'
//        ]);
//        $ticket->comments()->create([
//            'description' => $request->comment,
//            'user_id' => auth()->user()->id
//        ]);
//        return back();
//    }
//    public function unassigned(){
//        return view('backend.ticket.unassigned-table');
//    }

    public function index(Request $request)
    {
        $tickets = QueryBuilder::for(Ticket::class)
            ->allowedFilters([
                AllowedFilter::scope('assigned_status'),
                AllowedFilter::scope('assigned_agent'),
                'id',
                'title',
                'description',
                'status',
                'priority',
                'user_id',
                'category_id',
            ])->allowedIncludes(['comments', 'assigned_agent', 'category', 'files'])
            ->get();
        return response()->json($tickets);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket = QueryBuilder::for(Ticket::class)
            ->allowedIncludes(['comments', 'assigned_agent', 'category', 'files'])
            ->where('id', $ticket->id)
            ->first();
        return response()->json($ticket);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => ['required', 'integer', 'between:0,2'],
        ]);
        $ticket->update([
            'status' => $request->status
        ]);
        return response()->json(['message' => 'Ticket updated successfully', 'ticket' => $ticket]);
    }
}
