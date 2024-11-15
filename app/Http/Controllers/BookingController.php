<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();
        return response()->json($bookings, 200);
    }
    
    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'provider_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
        ]);

        $service = Service::find($validated['service_id']);
        if (!$service ) {
            return response()->json(['error' => 'Service is not found'], 422);
        }
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        $overlap = Booking::where('service_provider_id', $validated['provider_id'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })->exists();

        if ($overlap) {
            return response()->json(['error' => 'Time slot unavailable'], 422);
        }

        
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'],
            'service_provider_id' => $validated['provider_id'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
        ]);

        
        return response()->json($booking);
    }
    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        return response()->json($booking, 200);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'service_id' => 'exists:services,id',
            'service_provider_id' => 'exists:service_providers,id',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
            'status' => 'string'
        ]);

        $booking->update($validatedData);
        return response()->json($booking, 200);
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $booking->delete();
        return response()->json(['message' => 'Booking deleted successfully'], 200);
    }
}
