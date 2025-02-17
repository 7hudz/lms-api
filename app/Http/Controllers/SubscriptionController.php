<?php
namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request) {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = auth()->user();
        $course = Course::find($request->course_id);

        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }

        // Prevent duplicate subscriptions
        if (Subscription::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return response()->json(['error' => 'You are already subscribed to this course'], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $course->title],
                    'unit_amount' => $course->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => url('/success'),
            'cancel_url' => url('/cancel'),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'stripe_subscription_id' => $session->id,
            'status' => 'active'
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function cancelSubscription($id) {
        $subscription = Subscription::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Subscription cancelled']);
    }

    public function listSubscriptions() {
        $subscriptions = Subscription::where('user_id', Auth::id())->with('course')->get();
        return response()->json($subscriptions);
    }

    public function adminViewSubscriptions() {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(Subscription::with('user', 'course')->get());
    }
}
