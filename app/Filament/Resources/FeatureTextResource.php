<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeatureText;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureTextResource\Pages;
use App\Filament\Resources\FeatureTextResource\RelationManagers;

class FeatureTextResource extends Resource
{
    protected static ?string $model = FeatureText::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('text')->required(),
                Select::make('qrx_code_id') ->relationship( 'qrxCode', 'name')->searchable()->preload()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('text')->label('Text')->searchable(),
                TextColumn::make('qrxCode.name'),
            ])
            ->filters([

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
            'index' => Pages\ListFeatureTexts::route('/'),
            'create' => Pages\CreateFeatureText::route('/create'),
            'edit' => Pages\EditFeatureText::route('/{record}/edit'),
        ];
    }    
}
