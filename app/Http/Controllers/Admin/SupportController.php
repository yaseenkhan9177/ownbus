<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;

class SupportController extends Controller
{
    /**
     * Display a listing of tickets across the SaaS platform.
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'open');

        $tickets = Ticket::withoutGlobalScopes()
            ->with(['company', 'user'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->latest('last_activity_at')
            ->paginate(15);

        $counts = [
            'open' => Ticket::withoutGlobalScopes()->where('status', 'open')->count(),
            'in_progress' => Ticket::withoutGlobalScopes()->where('status', 'in_progress')->count(),
            'resolved' => Ticket::withoutGlobalScopes()->where('status', 'resolved')->count(),
            'closed' => Ticket::withoutGlobalScopes()->where('status', 'closed')->count(),
            'critical' => Ticket::withoutGlobalScopes()->where('status', 'open')->where('priority', 'critical')->count(),
        ];

        return view('admin.support.index', compact('tickets', 'status', 'counts'));
    }

    /**
     * Display a specific ticket chat interface.
     */
    public function show($id)
    {
        $ticket = Ticket::withoutGlobalScopes()
            ->with(['company', 'user'])
            ->findOrFail($id);

        $replies = TicketReply::withoutGlobalScopes()
            ->with('user')
            ->where('ticket_id', $ticket->id)
            ->oldest()
            ->get();

        return view('admin.support.show', compact('ticket', 'replies'));
    }

    /**
     * Store a reply to the ticket from the Super Admin.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $ticket = Ticket::withoutGlobalScopes()->findOrFail($id);

        $ticket->replies()->create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'body' => $request->body,
            'is_admin_reply' => true,
        ]);

        // Auto transition status if replying to an open ticket
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }

        $ticket->last_activity_at = now();
        $ticket->save();

        return redirect()->route('admin.support.show', $ticket->id)
            ->with('success', 'Reply sent successfully.');
    }

    /**
     * Quick-change the status of a ticket.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket = Ticket::withoutGlobalScopes()->findOrFail($id);
        $ticket->status = $request->status;
        $ticket->last_activity_at = now();
        $ticket->save();

        return redirect()->back()->with('success', 'Ticket status updated to ' . ucfirst($request->status) . '.');
    }
}
