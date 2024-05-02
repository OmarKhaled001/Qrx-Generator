<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeaturePhone;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeaturePhoneResource\Pages;
use App\Filament\Resources\FeaturePhoneResource\RelationManagers;

class FeaturePhoneResource extends Resource
{
    protected static ?string $model = FeaturePhone::class;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('phone')->tel()->suffixIcon('heroicon-m-phone')->required(),
                Select::make('qrx_code_id') ->relationship( 'qrxCode', 'name')->searchable()->preload()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('phone')->label('Phone')->searchable(),
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
            'index' => Pages\ListFeaturePhones::route('/'),
            'create' => Pages\CreateFeaturePhone::route('/create'),
            'edit' => Pages\EditFeaturePhone::route('/{record}/edit'),
        ];
    }    
}
