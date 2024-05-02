<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeatureUrl;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureUrlResource\Pages;
use App\Filament\Resources\FeatureUrlResource\RelationManagers;

class FeatureUrlResource extends Resource
{
    protected static ?string $model = FeatureUrl::class;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('url')->url()->suffixIcon('heroicon-o-link')->required(),
                Select::make('qrx_code_id') ->relationship( 'qrxCode', 'name')->searchable()->preload()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')->label('Url')->searchable(),
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
            'index' => Pages\ListFeatureUrls::route('/'),
            'create' => Pages\CreateFeatureUrl::route('/create'),
            'edit' => Pages\EditFeatureUrl::route('/{record}/edit'),
        ];
    }    
}
