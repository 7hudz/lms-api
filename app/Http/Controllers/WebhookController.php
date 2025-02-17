<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleStripeWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid webhook signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $subscription = Subscription::where('stripe_subscription_id', $event->data->object->id)->first();
            if ($subscription) {
                $subscription->update(['status' => 'active']);
            }
        }

        if ($event->type === 'customer.subscription.deleted') {
            $subscription = Subscription::where('stripe_subscription_id', $event->data->object->id)->first();
            if ($subscription) {
                $subscription->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['message' => 'Webhook handled successfully']);
    }
}

