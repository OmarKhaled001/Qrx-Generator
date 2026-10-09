<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use App\Services\Subscriptions;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Account'))->columns(2)->components([
                TextInput::make('name')->label(__('Full name'))->required()->maxLength(255),
                TextInput::make('email')->label(__('Email address'))->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label(__('Password'))
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->helperText(fn (string $operation) => $operation === 'edit' ? __('Leave empty to keep the current password.') : null),
                Select::make('locale')->label(__('Language'))->options(['en' => 'English', 'ar' => 'العربية'])->default('en'),
                Toggle::make('is_admin')
                    ->label(__('Administrator'))
                    ->helperText(__('Administrators can open this panel.'))
                    // Nobody can remove their own admin access by accident.
                    ->disabled(fn (?Model $record) => $record?->is(auth()->user()) ?? false),
            ]),
            Section::make(__('Contact details'))->columns(2)->collapsed()->components([
                TextInput::make('phone')->label(__('Phone number'))->tel()->maxLength(30),
                TextInput::make('country')->label(__('Country'))->maxLength(100),
                TextInput::make('city')->label(__('City'))->maxLength(100),
                TextInput::make('area')->label(__('Area'))->maxLength(100),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('subscriptions')->withCount('qrCodes'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label(__('Full name'))->searchable()->sortable(),
                TextColumn::make('email')->label(__('Email address'))->searchable()->copyable(),
                TextColumn::make('plan')
                    ->label(__('Plan'))
                    ->state(fn (User $record) => __(app(Subscriptions::class)->currentPlan($record)->name))
                    ->badge(),
                TextColumn::make('qr_codes_count')->label(__('QR codes'))->numeric()->sortable(),
                IconColumn::make('is_admin')->label(__('Admin'))->boolean(),
                IconColumn::make('email_verified_at')->label(__('Verified'))->boolean()->getStateUsing(fn (User $record) => $record->email_verified_at !== null),
                TextColumn::make('created_at')->label(__('Joined'))->date()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')->label(__('Administrators')),
                TernaryFilter::make('email_verified_at')->label(__('Verified'))->nullable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
