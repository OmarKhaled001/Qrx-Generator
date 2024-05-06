<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeatureVcard;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureVcardResource\Pages;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\FeatureVcardResource\RelationManagers;

class FeatureVcardResource extends Resource
{
    protected static ?string $model = FeatureVcard::class;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('full_name')->required(),
                Select::make('qrx_code_id') ->relationship('qrxCode','name')->searchable()->preload()->required(),
                TextInput::make('phone')->required(),
                TextInput::make('tel'),
                TextInput::make('email'),
                Textarea::make('address')->columnSpanFull(),
                Textarea::make('note')->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->label('Full name')->searchable(),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')->label('Phone')->searchable(),
                TextColumn::make('tel')->label('tel')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')->label('Address')->searchable()->limit(50)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('note')->label('Note')->searchable()->limit(50)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('qrxCode.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListFeatureVcards::route('/'),
            'create' => Pages\CreateFeatureVcard::route('/create'),
            'edit' => Pages\EditFeatureVcard::route('/{record}/edit'),
        ];
    }    
}
