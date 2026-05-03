<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $user = Auth::user();
        $bookId = $request->book_id;

        // Check if user already has an active reservation for this book
        $existingReservation = Reservation::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['waiting', 'ready'])
            ->first();

        if ($existingReservation) {
            return back()->with('error', 'You already have an active reservation for this book.');
        }

        // Create the reservation
        Reservation::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'status' => 'waiting',
        ]);

        return back()->with('success', 'Your reservation has been created. You will be notified when the book becomes available.');
    }

    /**
     * Cancel a reservation (student can only cancel 'waiting' reservations)
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $reservation = Reservation::where('user_id', $user->id)
            ->where('id', $id)
            ->where('status', 'waiting')
            ->firstOrFail();

        $reservation->delete();

        return back()->with('success', 'Reservation cancelled successfully.');
    }
}
