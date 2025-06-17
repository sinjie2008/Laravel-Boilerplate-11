<?php

namespace Modules\BillingManager\Tests\Feature\Front;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Facades\Cashier;
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
}
