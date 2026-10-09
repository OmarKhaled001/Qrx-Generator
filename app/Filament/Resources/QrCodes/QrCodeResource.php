<?php

namespace App\Filament\Resources\QrCodes;

use App\Enums\QrStatus;
use App\Enums\QrType;
use App\Filament\Resources\QrCodes\Pages\EditQrCode;
use App\Filament\Resources\QrCodes\Pages\ListQrCodes;
use App\Models\QrCode;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Every customer's QR codes. Admins can rename, pause or delete them;
 * content is edited by the owner in the dashboard builder.
 */
class QrCodeResource extends Resource
{
    protected static ?string $model = QrCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('QR code');
    }

    public static function getPluralModelLabel(): string
    {
        return __('QR codes');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('QR code'))->columns(2)->components([
                TextInput::make('name')->label(__('Name'))->required()->maxLength(120),
                Select::make('status')->label(__('Status'))->options(QrStatus::class)->required(),
                TextInput::make('code')->label(__('Short code'))->disabled()->dehydrated(false),
                Select::make('type')->label(__('Type'))->options(QrType::class)->disabled()->dehydrated(false),
            ]),
            Section::make(__('Content'))->components([
                KeyValue::make('payload')->hiddenLabel()->disabled()->dehydrated(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                TextColumn::make('user.name')->label(__('Owner'))->searchable()->description(fn (QrCode $record) => $record->user->email),
                TextColumn::make('type')->label(__('Type'))->badge(),
                TextColumn::make('status')->label(__('Status'))->badge(),
                TextColumn::make('scan_count')->label(__('Scans'))->numeric()->sortable(),
                TextColumn::make('code')->label(__('Short code'))->searchable()->copyable()->fontFamily('mono'),
                TextColumn::make('created_at')->label(__('Created'))->date()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('Type'))->options(QrType::class),
                SelectFilter::make('status')->label(__('Status'))->options(QrStatus::class),
            ])
            ->recordActions([
                Action::make('open')
                    ->label(__('Open'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (QrCode $record) => $record->shortUrl(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQrCodes::route('/'),
            'edit' => EditQrCode::route('/{record}/edit'),
        ];
    }
}
