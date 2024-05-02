<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Models\FeatureMessage;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureMessageResource\Pages;
use App\Filament\Resources\FeatureMessageResource\RelationManagers;

class FeatureMessageResource extends Resource
{
    protected static ?string $model = FeatureMessage::class;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-chat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')->tel()->suffixIcon('heroicon-m-phone')->required(),
                Select::make('qrx_code_id') ->relationship( 'qrxCode', 'name')->searchable()->preload()->required(),
                Textarea::make('message')->required()->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')->label('Phone')->searchable(),
                TextColumn::make('message')->label('Message')->searchable()->limit(50),
                TextColumn::make('qrxCode.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatureMessages::route('/'),
            'create' => Pages\CreateFeatureMessage::route('/create'),
            'edit' => Pages\EditFeatureMessage::route('/{record}/edit'),
        ];
    }    
}
