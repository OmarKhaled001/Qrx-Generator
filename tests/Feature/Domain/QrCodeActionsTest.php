<?php

namespace Tests\Feature\Domain;

use App\Actions\Qr\CreateQrCode;
use App\Actions\Qr\UpdateQrCode;
use App\Enums\QrStatus;
use App\Enums\QrType;
use App\Exceptions\QrQuotaExceeded;
use App\Models\Plan;
use App\Models\QrCode;
use App\Models\User;
use App\Support\QrDesign;
use App\Support\ShortCode;
use Database\Factories\QrCodeFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SubscribesUsers;
use Tests\TestCase;

class QrCodeActionsTest extends TestCase
{
    use RefreshDatabase, SubscribesUsers;

    public function test_every_type_can_be_created_with_a_unique_short_code(): void
    {
        $user = User::factory()->create();
        $this->subscribe($user, Plan::factory()->create(['qr_limit' => 20]));
        $create = app(CreateQrCode::class);

        foreach (QrType::cases() as $type) {
            $qrCode = $create->handle($user, 'My '.$type->value, $type, QrCodeFactory::samplePayload($type), QrDesign::defaults());

            $this->assertSame($type, $qrCode->type);
            $this->assertSame(QrStatus::Active, $qrCode->status);
            $this->assertSame(QrCodeFactory::samplePayload($type), $qrCode->fresh()->payload);
            $this->assertMatchesRegularExpression('/^[2-9a-hj-km-np-zA-HJ-NP-Z]{'.ShortCode::LENGTH.'}$/', $qrCode->code);
        }

        $this->assertSame(8, QrCode::query()->distinct()->count('code'));
    }

    public function test_creating_past_the_plan_limit_throws(): void
    {
        $this->freePlan();
        $user = User::factory()->create();
        QrCode::factory()->count(3)->for($user)->create();

        $this->expectException(QrQuotaExceeded::class);

        app(CreateQrCode::class)->handle($user, 'One too many', QrType::Text, ['text' => 'x'], []);
    }

    public function test_create_drops_unknown_payload_fields_and_normalizes_the_design(): void
    {
        $user = User::factory()->create();

        $qrCode = app(CreateQrCode::class)->handle($user, 'Link', QrType::Url, ['url' => 'https://a.test', 'extra' => 'x'], ['style' => 'dot', 'margin' => 99]);

        $this->assertSame(['url' => 'https://a.test'], $qrCode->payload);
        $this->assertSame('dot', $qrCode->design['style']);
        $this->assertSame(10, $qrCode->design['margin']);
    }

    public function test_changing_the_type_replaces_the_payload_entirely(): void
    {
        $qrCode = QrCode::factory()->ofType(QrType::Email)->create();

        app(UpdateQrCode::class)->handle($qrCode, 'Now SMS', QrType::Sms, ['phone' => '+201000000000', 'message' => 'Hi'], QrDesign::defaults());

        $fresh = $qrCode->fresh();
        $this->assertSame(QrType::Sms, $fresh->type);
        $this->assertSame(['phone' => '+201000000000', 'message' => 'Hi'], $fresh->payload);
        $this->assertSame('Now SMS', $fresh->name);
    }

    public function test_short_url_uses_the_x_route(): void
    {
        $qrCode = QrCode::factory()->create();

        $this->assertSame(url('/x/'.$qrCode->code), $qrCode->shortUrl());
    }
}
