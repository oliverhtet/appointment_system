<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{

    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments, 200);
    }


    public function pay($bookingId)
    {
      
        $booking = Booking::find($bookingId); 
        if(!$booking){
            return response()->json(['error' => 'Booking not found'], 404);
        } 
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $booking->service->price * 100,  
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);
        $payment = Payment::create([
            'booking_id' => $bookingId,
            'amount' => $booking->service->price,
            'status' => 'pending',
            'transaction_id' => $paymentIntent->id,
        ]);

        return response()->json(['client_secret' => $paymentIntent->client_secret]);
       
    }

    
    public function confirm(Request $request, $paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

           
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $paymentIntent = PaymentIntent::retrieve($payment->transaction_id);


            if ($paymentIntent->client_secret) {
                $payment->status = 'completed';
                $payment->save();

                $booking = $payment->booking;
                $booking->status = 'confirmed';
                $booking->save();

                return response()->json(['status' => 'Payment confirmed and booking completed']);
            } else {
                return response()->json(['error' => 'Payment was not successful',$paymentIntent], 400);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $payment->delete();
        return response()->json(['message' => 'Payment deleted successfully'], 200);
    }
}
