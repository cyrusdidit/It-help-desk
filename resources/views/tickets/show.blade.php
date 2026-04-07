@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-4">{{ $ticket->title }}</h2>

<div class="bg-moon-rock text-soft-dove p-6 rounded shadow space-y-2">
    <p><b>Name:</b> {{ $ticket->full_name }}</p>
    <p><b>Class / Department:</b> {{ $ticket->class_department }}</p>
    <p><b>Category:</b> {{ $ticket->category }}</p>
    <p><b>Priority:</b> 
        <span class="px-2 py-1 rounded
        @if($ticket->priority=='Low') bg-green-700
        @elseif($ticket->priority=='Medium') bg-yellow-700
        @else bg-red-700 @endif text-soft-dove font-bold">
            {{ $ticket->priority }}
        </span>
    </p>
    <p><b>Status:</b> 
        <span class="font-bold
        @if($ticket->status=='open') text-green-300
        @elseif($ticket->status=='closed') text-red-300
        @else text-yellow-300 @endif">
            {{ ucfirst($ticket->status) }}
        </span>
    </p>

    @if($ticket->assigned_to)
        <p><b>Assigned to:</b> {{ $ticket->assignedTo?->name ?? 'Unknown' }}</p>
    @elseif(auth()->user()->role === 'it')
        <form method="POST" action="/tickets/{{ $ticket->id }}/claim" class="mt-2">
            @csrf
            <button type="submit" class="bg-green-700 px-4 py-2 rounded hover:bg-green-800 text-soft-dove font-bold">
                Claim Ticket
            </button>
        </form>
    @endif

    <hr class="my-2 border-soft-dove">

    <p>{{ $ticket->description }}</p>

    <h3 class="mt-4 font-semibold">Attachments</h3>
    <div class="flex flex-wrap gap-4 mt-2">
        @foreach($ticket->attachments as $attachment)
            @php
                $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
            @endphp
            @if($isImage)
                <img src="/storage/{{ $attachment->file_path }}" class="w-32 h-32 object-cover rounded">
            @else
                <a href="/storage/{{ $attachment->file_path }}" target="_blank" class="bg-dark-sienna text-soft-dove px-4 py-2 rounded hover:bg-black-raspberry">
                    Download {{ strtoupper($extension) }} file
                </a>
            @endif
        @endforeach
    </div>

    <h3 class="mt-4 font-semibold">Comments</h3>
    <div class="space-y-2">
        @foreach($ticket->comments as $c)
            <p><b>{{ $c->user->name }}:</b> {{ $c->comment }}</p>
        @endforeach
    </div>

    <form method="POST" action="/tickets/{{ $ticket->id }}/comment" class="mt-4">
        @csrf
        <textarea name="comment" placeholder="Write a comment" class="w-full p-2 rounded text-black mb-2"></textarea>
        <button class="bg-dark-sienna px-4 py-2 rounded hover:bg-black-raspberry text-soft-dove font-bold">Send</button>
    </form>

    <div class="mt-4 space-x-2">
        @if(auth()->user()->role === 'it' && $ticket->assigned_to === auth()->id() && $ticket->status !== 'closed')
            <form method="POST" action="/tickets/{{ $ticket->id }}/complete" class="inline">
                @csrf
                <button class="bg-green-600 px-4 py-2 rounded hover:bg-green-700 text-white font-bold">Complete Ticket</button>
            </form>
        @endif

        <a href="/tickets/{{ $ticket->id }}/edit" class="bg-moon-rock px-4 py-2 rounded hover:bg-spiced-hot-chocolate text-soft-dove">Edit Ticket</a>

        <form method="POST" action="/tickets/{{ $ticket->id }}" class="inline">
            @csrf
            @method('DELETE')
            <button onclick="return confirm('Delete ticket?')" class="bg-dark-sienna px-4 py-2 rounded hover:bg-black-raspberry text-soft-dove">Delete Ticket</button>
        </form>
    </div>
</div>
@endsection
