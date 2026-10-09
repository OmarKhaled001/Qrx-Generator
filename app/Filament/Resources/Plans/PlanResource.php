<?php

namespace App\Filament\Resources\Plans;

use App\Filament\Resources\Plans\Pages\CreatePlan;
use App\Filament\Resources\Plans\Pages\EditPlan;
use App\Filament\Resources\Plans\Pages\ListPlans;
use App\Models\Plan;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        // Plan names are translation keys ("Business"), shown translated like in the app.
        return $record instanceof Plan ? __($record->name) : null;
    }

    public static function getModelLabel(): string
    {
        return __('Plan');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Plans');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Plan'))->columns(2)->components([
                TextInput::make('name')->label(__('Name'))->required()->maxLength(60),
                TextInput::make('slug')->label(__('Slug'))->required()->alphaDash()->maxLength(60)->unique(ignoreRecord: true)
                    ->helperText(__('Used in the checkout URL, e.g. /billing/checkout/business.')),
                Toggle::make('is_free')->label(__('Free plan'))->live()
                    ->helperText(__('Everyone without a paid subscription is on the free plan. Keep exactly one.')),
                Toggle::make('is_active')->label(__('Active'))->default(true)
                    ->helperText(__('Inactive plans are hidden and cannot be bought.')),
            ]),
            Section::make(__('Price'))->columns(3)->hidden(fn (Get $get) => (bool) $get('is_free'))->components([
                TextInput::make('stripe_price_id')
                    ->label(__('Stripe price ID'))
                    ->placeholder('price_…')
                    ->required(fn (Get $get) => ! $get('is_free'))
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->whereNotNull('stripe_price_id'))
                    ->columnSpan(3),
                TextInput::make('price_cents')
                    ->label(__('Price shown (per month)'))
                    ->numeric()->minValue(0)->step(0.01)->prefix('$')
                    // Stored in cents; edited in currency units.
                    ->formatStateUsing(fn (?int $state) => $state === null ? null : $state / 100)
                    ->dehydrateStateUsing(fn ($state) => (int) round(((float) $state) * 100)),
                TextInput::make('currency')->label(__('Currency'))->default('usd')->maxLength(3),
                TextInput::make('interval')->label(__('Billing interval'))->default('month')->maxLength(10),
            ]),
            Section::make(__('Limits'))->columns(3)->components([
                TextInput::make('qr_limit')->label(__('QR codes'))->numeric()->minValue(1)->required(),
                TextInput::make('scan_limit')->label(__('Scans per QR code'))->numeric()->minValue(1)->required(),
                TextInput::make('sort_order')->label(__('Order'))->numeric()->default(0),
                TagsInput::make('features')->label(__('Feature list'))->helperText(__('Shown on the pricing page. Add Arabic translations in lang/ar.json.'))->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                // Names are translation keys, like everywhere else in the app; slugs and Stripe IDs are identifiers (translate="no").
                TextColumn::make('name')->label(__('Name'))->searchable()->formatStateUsing(fn (string $state) => __($state))
                    ->description(fn (Plan $record) => new HtmlString('<span translate="no" dir="ltr">'.e($record->slug).'</span>')),
                TextColumn::make('price_cents')->label(__('Price'))->state(fn (Plan $record) => $record->formattedPrice()),
                TextColumn::make('qr_limit')->label(__('QR codes'))->numeric(),
                TextColumn::make('scan_limit')->label(__('Scans per QR code'))->numeric(),
                TextColumn::make('stripe_price_id')->label(__('Stripe price ID'))->placeholder('—')->copyable()->toggleable()->extraAttributes(['translate' => 'no', 'dir' => 'ltr']),
                IconColumn::make('is_free')->label(__('Free plan'))->boolean(),
                ToggleColumn::make('is_active')->label(__('Active')),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlans::route('/'),
            'create' => CreatePlan::route('/create'),
            'edit' => EditPlan::route('/{record}/edit'),
        ];
    }
}
