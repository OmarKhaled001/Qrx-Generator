<?php

namespace App\Filament\Resources\Contacts;

use App\Filament\Resources\Contacts\Pages\ManageContacts;
use App\Models\Contact;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/**
 * Messages from the landing-page contact form. Read-only, with mark-as-read.
 */
class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return __('Message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Messages');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = Contact::query()->whereNull('read_at')->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label(__('Name')),
            TextEntry::make('email')->label(__('Email address'))->copyable(),
            TextEntry::make('subject')->label(__('Subject'))->columnSpanFull(),
            TextEntry::make('message')->label(__('Message'))->columnSpanFull()->prose(),
            TextEntry::make('created_at')->label(__('Received'))->dateTime(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')->label(__('Subject'))->searchable()
                    ->weight(fn (Contact $record) => $record->read_at ? FontWeight::Normal : FontWeight::Bold)
                    ->description(fn (Contact $record) => $record->name.' · '.$record->email),
                TextColumn::make('created_at')->label(__('Received'))->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label(__('Read'))->nullable(),
            ])
            ->recordActions([
                ViewAction::make()->after(fn (Contact $record) => $record->read_at ?? $record->forceFill(['read_at' => now()])->save()),
                Action::make('markRead')
                    ->label(__('Mark as read'))
                    ->icon(Heroicon::OutlinedCheck)
                    ->hidden(fn (Contact $record) => $record->read_at !== null)
                    ->action(fn (Contact $record) => $record->forceFill(['read_at' => now()])->save()),
                Action::make('reply')
                    ->label(__('Reply'))
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->url(fn (Contact $record) => 'mailto:'.$record->email.'?subject='.rawurlencode('Re: '.$record->subject)),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContacts::route('/'),
        ];
    }
}
