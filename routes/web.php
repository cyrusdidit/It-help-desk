<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Models\Ticket;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'it') {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $assignedToMe = Ticket::where('assigned_to', $user->id)->count();
        $closedTickets = Ticket::where('status', 'closed')->count();
        $recentTickets = Ticket::latest()->take(3)->get();
    } else {
        $totalTickets = Ticket::where('user_id', $user->id)->count();
        $openTickets = Ticket::where('user_id', $user->id)->where('status', 'open')->count();
        $assignedToMe = 0;
        $closedTickets = Ticket::where('user_id', $user->id)->where('status', 'closed')->count();
        $recentTickets = Ticket::where('user_id', $user->id)->latest()->take(3)->get();
    }

    return view('dashboard', compact('totalTickets', 'openTickets', 'assignedToMe', 'closedTickets', 'recentTickets'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tickets', [TicketController::class,'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class,'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketController::class,'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketController::class,'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketController::class,'destroy'])->name('tickets.destroy');
    Route::post('/tickets/{ticket}/comment', [TicketController::class,'addComment'])->name('tickets.addComment');
    Route::post('/tickets/{ticket}/claim', [TicketController::class,'claim'])->name('tickets.claim');
    Route::post('/tickets/{ticket}/complete', [TicketController::class,'complete'])->name('tickets.complete');

});

require __DIR__.'/auth.php';
