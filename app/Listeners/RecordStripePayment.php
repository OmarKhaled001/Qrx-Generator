<?php

namespace App\Listeners;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Cashier\Events\WebhookReceived;

/**
 * Records paid Stripe invoices for admin revenue reporting. Idempotent per
 * invoice, because Stripe retries webhooks.
 */
class RecordStripePayment
{
    public function handle(WebhookReceived $event): void
    {
        if (($event->payload['type'] ?? null) !== 'invoice.paid') {
            return;
        }

        $invoice = $event->payload['data']['object'] ?? [];

        if (empty($invoice['id']) || ($invoice['amount_paid'] ?? 0) <= 0) {
            return;
        }

        $paidAt = $invoice['status_transitions']['paid_at'] ?? $invoice['created'] ?? null;

        Payment::query()->updateOrCreate(
            ['stripe_invoice_id' => $invoice['id']],
            [
                'user_id' => User::query()->where('stripe_id', $invoice['customer'] ?? null)->value('id'),
                'amount_cents' => (int) $invoice['amount_paid'],
                'currency' => strtolower($invoice['currency'] ?? 'usd'),
                'paid_at' => $paidAt ? Carbon::createFromTimestamp($paidAt) : now(),
            ],
        );
    }
}
