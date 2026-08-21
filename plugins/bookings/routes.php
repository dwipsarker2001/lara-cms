<?php

use App\Support\NotificationCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::post('/bookings', function (Request $request) {
    $booking = (object) [
        'reference' => 'BK-'.strtoupper(Str::random(6)),
        'customer_name' => $request->input('customer_name', 'John Doe'),
        'tour_title' => $request->input('tour_title', 'City Tour'),
    ];

    NotificationCenter::success(
        "New Booking: {$booking->reference}",
        "{$booking->customer_name} booked {$booking->tour_title}",
        url: route('admin.bookings.index')
    );

    return response()->json(['success' => true, 'booking' => $booking]);
})->name('bookings.store');
