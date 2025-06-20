<?php

declare(strict_types=1);

namespace Modules\Billing\App\Services;

use Modules\Billing\App\Models\Coupon;
use Modules\Billing\App\Models\Plan;
use Modules\Billing\App\Models\Subscription;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function createPlan(array $data): string
    {
        $product = $this->client->products->create(['name' => $data['name']]);
        $price = $this->client->prices->create([
            'unit_amount' => (int) ($data['amount'] * 100),
            'currency' => 'usd',
            'recurring' => ['interval' => $data['billing_interval']],
            'product' => $product->id,
        ]);

        return $price->id;
    }

    public function updatePlan(Plan $plan, array $data): void
    {
        if ($plan->stripe_id) {
            $this->client->prices->update($plan->stripe_id, [
                'active' => $data['status'] === 'active',
            ]);
        }
    }

    public function deletePlan(Plan $plan): void
    {
        if ($plan->stripe_id) {
            $this->client->prices->update($plan->stripe_id, ['active' => false]);
        }
    }

    public function createCoupon(array $data): string
    {
        $coupon = $this->client->coupons->create([
            'id' => $data['code'],
            'duration' => $data['duration'],
            'amount_off' => $data['amount_off'] ? (int) ($data['amount_off'] * 100) : null,
            'percent_off' => $data['percent_off'],
        ]);

        return $coupon->id;
    }

    public function updateCoupon(Coupon $coupon, array $data): void
    {
        if ($coupon->stripe_id) {
            $this->client->coupons->update($coupon->stripe_id, [
                'metadata' => ['applies_to' => $data['applies_to'] ?? ''],
            ]);
        }
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        if ($coupon->stripe_id) {
            $this->client->coupons->delete($coupon->stripe_id);
        }
    }

    public function refund(string $invoiceId, ?float $amount = null): void
    {
        $params = ['payment_intent' => $invoiceId];
        if ($amount) {
            $params['amount'] = (int) ($amount * 100);
        }

        $this->client->refunds->create($params);
    }

    public function retrySubscriptionPayment(Subscription $subscription): void
    {
        if ($subscription->latest_invoice) {
            $this->client->invoices->pay($subscription->latest_invoice, ['off_session' => true]);
        }
    }
}
