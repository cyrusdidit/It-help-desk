@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">{{ isset($ticket) ? 'Edit Ticket' : 'Create Ticket' }}</h2>

<form method="POST" action="{{ isset($ticket) ? '/tickets/'.$ticket->id : '/tickets' }}" enctype="multipart/form-data" class="bg-moon-rock text-soft-dove p-6 rounded shadow space-y-4">
    @csrf
    @if(isset($ticket)) @method('PUT') @endif

    <input type="text" name="full_name" value="{{ $ticket->full_name ?? old('full_name') }}" placeholder="Full Name" class="w-full p-2 rounded text-black">
    <input type="text" name="class_department" value="{{ $ticket->class_department ?? old('class_department') }}" placeholder="Class / Department" class="w-full p-2 rounded text-black">

    <select name="category" class="w-full p-2 rounded text-black">
        <option {{ (isset($ticket) && $ticket->category=='Hardware') ? 'selected' : '' }}>Hardware</option>
        <option {{ (isset($ticket) && $ticket->category=='Software') ? 'selected' : '' }}>Software</option>
        <option {{ (isset($ticket) && $ticket->category=='Network') ? 'selected' : '' }}>Network</option>
    </select>

    <select name="priority" class="w-full p-2 rounded text-black">
        <option {{ (isset($ticket) && $ticket->priority=='Low') ? 'selected' : '' }}>Low</option>
        <option {{ (isset($ticket) && $ticket->priority=='Medium') ? 'selected' : '' }}>Medium</option>
        <option {{ (isset($ticket) && $ticket->priority=='High') ? 'selected' : '' }}>High</option>
    </select>

    <input type="text" name="title" value="{{ $ticket->title ?? old('title') }}" placeholder="Title" class="w-full p-2 rounded text-black">
    <textarea name="description" placeholder="Describe problem" class="w-full p-2 rounded text-black">{{ $ticket->description ?? old('description') }}</textarea>

    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt">

    <button type="submit" class="bg-dark-sienna px-6 py-2 rounded hover:bg-black-raspberry text-soft-dove font-bold">
        {{ isset($ticket) ? 'Update Ticket' : 'Create Ticket' }}
    </button>
</form>
@endsection
