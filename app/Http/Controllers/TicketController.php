<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Attachment;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;


class TicketController extends Controller
{
    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'class_department' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'title' => 'required',
            'description' => 'required',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,txt|max:10240',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'class_department' => $request->class_department,
            'category' => $request->category,
            'priority' => $request->priority,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if($request->hasFile('attachments')){
            foreach($request->file('attachments') as $attachment){
                $path = $this->storeAttachmentWithOriginalName($attachment);

                Attachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path
                ]);
            }
        }

        return redirect('/dashboard')->with('success','Ticket created!');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $view = $request->query('view');

        $tickets = Ticket::query();

        if ($user->role === 'it') {
            if ($view === 'assigned') {
                $tickets->where('assigned_to', $user->id);
                $heading = 'Assigned to Me';
            } elseif ($view === 'all') {
                $heading = 'All Tickets';
            } else {
                $tickets->where('user_id', $user->id);
                $heading = 'My Submitted Tickets';
                $view = 'submitted';
            }
        } else {
            $tickets->where('user_id', $user->id);
            $heading = 'My Tickets';
            $view = 'my';
        }

        $maxTicketId = (clone $tickets)->max('id');

        if ($request->search) {
            $tickets->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $ticketId = $request->filled('ticket_id') ? (int) $request->ticket_id : null;

        if ($ticketId !== null && $ticketId <= 0) {
            $ticketId = null;
        }

        if ($ticketId !== null && $maxTicketId !== null) {
            $ticketId = min($ticketId, (int) $maxTicketId);
            $tickets->where('id', $ticketId);
        }

        if ($request->status) {
            if ($request->status === 'in_progress') {
                $tickets->where('status', 'assigned');
            } else {
                $tickets->where('status', $request->status);
            }
        }

        if ($request->priority) {
            $tickets->where('priority', $request->priority);
        }

        $tickets = $tickets->latest()->paginate(10);

        return view('tickets.index', [
            'tickets' => $tickets,
            'heading' => $heading,
            'view' => $view,
            'search' => $request->search,
            'ticket_id' => $ticketId,
            'max_ticket_id' => $maxTicketId,
            'status' => $request->status,
            'priority' => $request->priority,
            'currentView' => $request->view,
        ]);
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it') {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }

    public function claim(Ticket $ticket)
    {
        if (auth()->user()->role !== 'it') {
            abort(403);
        }

        if ($ticket->assigned_to) {
            return redirect('/tickets/'.$ticket->id)->with('error', 'Ticket is already claimed.');
        }

        $ticket->update([
            'assigned_to' => auth()->id(),
            'status' => 'assigned',
        ]);

        return redirect('/tickets/'.$ticket->id)->with('success', 'Ticket claimed successfully.');
    }

    public function complete(Ticket $ticket)
    {
        if (auth()->user()->role !== 'it') {
            abort(403);
        }

        if ($ticket->assigned_to !== auth()->id()) {
            return redirect('/tickets/'.$ticket->id)->with('error', 'You can only complete tickets assigned to you.');
        }

        $ticket->update([
            'status' => 'closed',
        ]);

        return redirect('/dashboard')->with('completion_success', 'Ticket completed successfully.');
    }

    public function edit(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it') {
            abort(403);
        }

        return view('tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it') {
            abort(403);
        }

        $request->validate([
            'full_name' => 'required',
            'class_department' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'title' => 'required',
            'description' => 'required',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx,txt|max:10240',
        ]);

        $ticket->update($request->only([
            'full_name',
            'class_department',
            'category',
            'priority',
            'title',
            'description'
        ]));

        if($request->hasFile('attachments')){
            foreach($request->file('attachments') as $attachment){
                $path = $this->storeAttachmentWithOriginalName($attachment);

                Attachment::create([
                    'ticket_id' => $ticket->id,
                    'file_path' => $path
                ]);
            }
        }

        return redirect('/tickets/'.$ticket->id)
                ->with('success','Ticket updated!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        if($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it'){
            abort(403);
        }

        $request->validate([
            'comment' => 'required'
        ]);

        Comment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment
        ]);

        return redirect('/tickets/'.$ticket->id);
    }

    public function destroy(Ticket $ticket)
    {
    if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it') {
        abort(403);
    }

    $ticket->delete();

    return redirect('/tickets?view=all')
            ->with('success','Ticket deleted!');
}

    public function showAttachment(Request $request, Ticket $ticket, Attachment $attachment)
    {
        if ($attachment->ticket_id !== $ticket->id) {
            abort(404);
        }

        if ($ticket->user_id !== auth()->id() && auth()->user()->role !== 'it') {
            abort(403);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'Attachment file not found on server.');
        }

        $fullPath = Storage::disk('public')->path($attachment->file_path);
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        $downloadName = basename($attachment->file_path);

        if ($request->boolean('download')) {
            return response()->download($fullPath, $downloadName, ['Content-Type' => $mime]);
        }

        // Force inline for preview routes (including online office viewers).
        if ($request->boolean('preview')) {
            return response()->file($fullPath, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
            ]);
        }

        // Preview common browser-viewable files; download the rest.
        if (str_starts_with($mime, 'image/') || in_array($mime, ['application/pdf', 'text/plain'])) {
            return response()->file($fullPath, ['Content-Type' => $mime]);
        }

        return response()->download($fullPath, $downloadName, ['Content-Type' => $mime]);
    }

    private function storeAttachmentWithOriginalName(UploadedFile $file): string
    {
        $directory = 'tickets';
        $originalName = $file->getClientOriginalName();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        $candidate = $originalName;
        $counter = 1;

        while (Storage::disk('public')->exists($directory.'/'.$candidate)) {
            $suffix = ' ('.$counter.')';
            $candidate = $baseName.$suffix.($extension ? '.'.$extension : '');
            $counter++;
        }

        return $file->storeAs($directory, $candidate, 'public');
    }

}
