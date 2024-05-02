<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeatureWifi;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureWifiResource\Pages;
use App\Filament\Resources\FeatureWifiResource\RelationManagers;

class FeatureWifiResource extends Resource
{
    protected static ?string $model = FeatureWifi::class;

    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('wireless_ssid')->required(),
                Select::make('qrx_code_id') ->relationship('qrxCode','name')->searchable()->preload()->required(),
                Select::make('encryption')
                ->options([
                    'WPA/WEP' => 'WPA/WEP',
                    'WEP' => 'WEP',
                ]),
                TextInput::make('password')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wireless_ssid')->label('SSID')->searchable(),
                TextColumn::make('encryption')->label('Encryption')->searchable(),
                TextColumn::make('password')->label('Password')->searchable()->toggledHiddenByDefault(true),
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
            'index' => Pages\ListFeatureWifis::route('/'),
            'create' => Pages\CreateFeatureWifi::route('/create'),
            'edit' => Pages\EditFeatureWifi::route('/{record}/edit'),
        ];
    }    
}
