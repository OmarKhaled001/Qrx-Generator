<?php

namespace Tests\Concerns;

use RuntimeException;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\Util\CaseInsensitiveArray;

/**
 * Answers Stripe API calls locally. $stripeCalls records "METHOD /path" for each
 * call and $stripeParams the parameters of the last call per path.
 */
trait FakesStripe
{
    /** @var list<string> */
    protected array $stripeCalls = [];

    /** @var array<string, array<string, mixed>> */
    protected array $stripeParams = [];

    /**
     * @param  array<string, array<string, mixed>>  $responses  path => JSON body
     */
    protected function fakeStripe(array $responses): void
    {
        config(['cashier.secret' => 'sk_test_fake', 'cashier.key' => 'pk_test_fake']);

        $calls = &$this->stripeCalls;
        $params = &$this->stripeParams;

        ApiRequestor::setHttpClient(new class($responses, $calls, $params) implements ClientInterface
        {
            public function __construct(private array $responses, private array &$calls, private array &$params) {}

            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                $path = parse_url($absUrl, PHP_URL_PATH);
                $this->calls[] = strtoupper($method).' '.$path;
                $this->params[$path] = $params;

                $body = $this->responses[$path] ?? throw new RuntimeException("Unexpected Stripe call: {$method} {$path}");

                return [json_encode($body), 200, new CaseInsensitiveArray([])];
            }
        });
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);

        parent::tearDown();
    }
}
