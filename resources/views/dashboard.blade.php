@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-4">Welcome back, {{ auth()->user()->name }}</h1>
<p class="mb-6 text-soft-dove/80">Role: <span class="font-semibold text-soft-dove">{{ ucfirst(auth()->user()->role) }}</span></p>

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-6">
    <div class="bg-moon-rock p-6 rounded-xl shadow-lg border border-soft-dove/20">
        <p class="text-sm uppercase tracking-wide text-soft-dove/70">Total Tickets</p>
        <p class="mt-4 text-4xl font-bold">{{ $totalTickets }}</p>
        <p class="mt-2 text-sm text-soft-dove/75">All tickets in your account{{ auth()->user()->role === 'it' ? '' : ' created by you' }}.</p>
    </div>

    <div class="bg-moon-rock p-6 rounded-xl shadow-lg border border-soft-dove/20">
        <p class="text-sm uppercase tracking-wide text-soft-dove/70">Open Tickets</p>
        <p class="mt-4 text-4xl font-bold">{{ $openTickets }}</p>
        <p class="mt-2 text-sm text-soft-dove/75">Tickets still waiting on support.</p>
    </div>

    @if(auth()->user()->role === 'it')
        <div class="bg-moon-rock p-6 rounded-xl shadow-lg border border-soft-dove/20">
            <p class="text-sm uppercase tracking-wide text-soft-dove/70">Assigned to me</p>
            <p class="mt-4 text-4xl font-bold">{{ $assignedToMe }}</p>
            <p class="mt-2 text-sm text-soft-dove/75">Tickets currently claimed by you.</p>
        </div>
    @else
        <div class="bg-moon-rock p-6 rounded-xl shadow-lg border border-soft-dove/20">
            <p class="text-sm uppercase tracking-wide text-soft-dove/70">Closed Tickets</p>
            <p class="mt-4 text-4xl font-bold">{{ $closedTickets ?? 0 }}</p>
            <p class="mt-2 text-sm text-soft-dove/75">Requests that have been resolved.</p>
        </div>
    @endif

    <div class="bg-spiced-hot-chocolate p-6 rounded-xl shadow-lg border border-soft-dove/20 text-soft-dove">
        <p class="text-sm uppercase tracking-wide text-soft-dove/70">Quick Actions</p>
        <div class="mt-4 space-y-3">
            <a href="/tickets/create" class="block rounded-xl bg-dark-sienna px-4 py-3 text-sm font-semibold text-soft-dove hover:bg-black-raspberry">Create a ticket</a>
            <a href="/tickets?view=my" class="block rounded-xl bg-moon-rock px-4 py-3 text-sm font-semibold text-soft-dove hover:bg-spiced-hot-chocolate">My tickets</a>
            @if(auth()->user()->role === 'it')
                <a href="/tickets?view=all" class="block rounded-xl bg-moon-rock px-4 py-3 text-sm font-semibold text-soft-dove hover:bg-spiced-hot-chocolate">All tickets</a>
            @endif
        </div>
    </div>
</div>

<div class="bg-moon-rock p-6 rounded-xl shadow-lg border border-soft-dove/20">
    <div class="flex items-center justify-between mb-4 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Recent Tickets</h2>
            <p class="text-sm text-soft-dove/70">Your latest ticket activity at a glance.</p>
        </div>
        <a href="/tickets" class="text-sm text-soft-dove/80 hover:text-soft-dove">View all</a>
    </div>

    @if($recentTickets->isEmpty())
        <div class="rounded-xl border border-soft-dove/20 bg-dark-sienna p-6 text-soft-dove/80">
            No recent tickets yet. Create one to get started.
        </div>
    @else
        <div class="space-y-3">
            @foreach($recentTickets as $ticket)
                <a href="/tickets/{{ $ticket->id }}" class="block rounded-xl border border-soft-dove/20 bg-moon-rock p-4 hover:bg-spiced-hot-chocolate transition">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-lg">{{ $ticket->title }}</p>
                            <p class="mt-1 text-sm text-soft-dove/70">{{ ucfirst($ticket->category) }} • {{ ucfirst($ticket->priority) }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold 
                            @if($ticket->status === 'open') bg-green-700
                            @elseif($ticket->status === 'closed') bg-red-700
                            @else bg-yellow-700 @endif text-soft-dove">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    @if(auth()->user()->role === 'it')
                        <p class="mt-3 text-sm text-soft-dove/70">Submitted by: {{ $ticket->user?->name ?? 'Unknown' }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
