<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\Util\CaseInsensitiveArray;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> "METHOD path" of every Stripe API call made */
    private array $stripeCalls = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['cashier.secret' => 'sk_test_fake']);

        // Answer Stripe API calls locally instead of over the network.
        $calls = &$this->stripeCalls;
        ApiRequestor::setHttpClient(new class($calls) implements ClientInterface
        {
            public function __construct(private array &$calls)
            {
            }

            public function request($method, $absUrl, $headers, $params, $hasFile)
            {
                $path = parse_url($absUrl, PHP_URL_PATH);
                $this->calls[] = strtoupper($method).' '.$path;

                $body = match ($path) {
                    '/v1/customers' => ['id' => 'cus_test', 'object' => 'customer'],
                    '/v1/checkout/sessions' => [
                        'id' => 'cs_test',
                        'object' => 'checkout.session',
                        'url' => 'https://checkout.stripe.com/c/pay/cs_test',
                    ],
                    default => throw new \RuntimeException('Unexpected Stripe call: '.$path),
                };

                return [json_encode($body), 200, new CaseInsensitiveArray([])];
            }
        });
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);

        parent::tearDown();
    }

    public function test_checkout_redirects_to_stripe_and_records_the_transaction(): void
    {
        $user = User::factory()->create();
        $plan = Plan::create([
            'plan_id' => 'prod_starter',
            'price_id' => 'price_starter',
            'title' => 'Starter',
            'price' => 9.99,
            'qr_count' => 10,
            'scan_count' => 1000,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('checkout', $plan->id))
            ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test');

        $this->assertSame(['POST /v1/customers', 'POST /v1/checkout/sessions'], $this->stripeCalls);
        $this->assertSame('cus_test', $user->fresh()->stripe_id);
        $this->assertEquals(9.99, (float) Transaction::sole()->amount);
    }

    public function test_guests_must_log_in_before_checkout(): void
    {
        $this->get(route('checkout', 1))->assertRedirect(route('login'));
    }
}
