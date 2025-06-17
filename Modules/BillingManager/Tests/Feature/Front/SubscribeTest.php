<?php

namespace Modules\BillingManager\Tests\Feature\Front;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Cashier;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe(): void
    {
        Cashier::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('billing.subscribe'), ['price' => 'price_test'])
            ->assertRedirect();

        Cashier::assertCharges(1);
    }

    public function test_user_can_resume_subscription(): void
    {
        Cashier::fake();
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post(route('billing.subscribe'), ['price' => 'price_test']);

        $this->post(route('billing.cancel'));

        $this->post(route('billing.resume'))
            ->assertRedirect();

        Cashier::assertCharges(1);
    }
}
