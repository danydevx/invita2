<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\ActivityService;
use App\Services\FeatureService;
use App\Services\UserNotificationService;
use App\Services\WebhookService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $query = SupportTicket::query()
            ->where('user_id', $request->user()->id);

        if ($request->filled('search')) {
            $query->where('subject', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query
            ->orderByDesc('last_reply_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($ticket) => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'last_reply_at' => $ticket->last_reply_at?->toDateTimeString(),
                'created_at' => $ticket->created_at?->toDateString(),
            ]);

        return Inertia::render('Member/Support/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    public function create(FeatureService $features)
    {
        if (! $features->enabled(request()->user(), 'module_support', true)) {
            return redirect('/member')->with('error', 'El modulo de soporte no esta habilitado.');
        }

        $departments = \App\Models\SupportDepartment::active()
            ->ordered()
            ->get()
            ->map(fn ($dept) => [
                'value' => $dept->id,
                'label' => $dept->name,
            ]);

        return Inertia::render('Member/Support/Create', [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'can_create_tickets', true)) {
            return back()->withErrors(['message' => 'No tienes permitido crear tickets.']);
        }
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'department_id' => ['nullable', 'integer', 'exists:support_departments,id'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $department = isset($data['department_id']) ? \App\Models\SupportDepartment::find($data['department_id']) : null;

        $ticket = SupportTicket::create([
            'user_id' => $request->user()->id,
            'subject' => trim($data['subject']),
            'message' => $data['message'],
            'status' => 'open',
            'priority' => $data['priority'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'last_reply_at' => now(),
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ]);

        $activity->log('ticket_created', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $ticket,
            'description' => 'Ticket creado',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($request->user(), 'ticket.created', [
            'ticket_id' => $ticket->id,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
        ]);

        $notifications->create(
            $request->user(),
            'support',
            'Ticket creado',
            'Tu ticket fue creado correctamente.',
            '/member/support/'.$ticket->id
        );

        return redirect()->route('member.support.show', $ticket)->with('success', 'Tu ticket fue creado correctamente.');
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['messages.user:id,name,email', 'department']);

        return Inertia::render('Member/Support/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'department' => $ticket->department?->name,
                'last_reply_at' => $ticket->last_reply_at?->toDateTimeString(),
                'created_at' => $ticket->created_at?->toDateString(),
                'closed_at' => $ticket->closed_at?->toDateString(),
                'messages' => $ticket->messages->map(fn ($message) => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_admin' => (bool) $message->is_admin,
                    'author' => $message->user ? [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'email' => $message->user->email,
                    ] : null,
                    'created_at' => $message->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket, ActivityService $activity, UserNotificationService $notifications, WebhookService $webhooks, FeatureService $features)
    {
        if (! $features->enabled($request->user(), 'module_support', true)) {
            return back()->withErrors(['message' => 'El modulo de soporte no esta habilitado.']);
        }
        $this->authorize('reply', $ticket);

        if ($ticket->status === 'closed') {
            return back()->with('error', 'El ticket esta cerrado.');
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'is_admin' => false,
            'message' => $data['message'],
        ]);

        $ticket->update([
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $activity->log('ticket_replied', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $ticket,
            'description' => 'Respuesta de usuario',
            'request' => $request,
        ]);

        $webhooks->dispatchUserEvent($request->user(), 'ticket.replied', [
            'ticket_id' => $ticket->id,
            'status' => $ticket->status,
        ]);

        $notifications->create(
            $request->user(),
            'support',
            'Respuesta enviada',
            'Tu respuesta fue enviada.',
            '/member/support/'.$ticket->id
        );

        return back()->with('success', 'Tu respuesta fue enviada.');
    }
}
