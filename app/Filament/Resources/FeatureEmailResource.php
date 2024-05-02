<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\FeatureEmail;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeatureEmailResource\Pages;
use App\Filament\Resources\FeatureEmailResource\RelationManagers;

class FeatureEmailResource extends Resource
{
    protected static ?string $model = FeatureEmail::class;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')->email()->suffixIcon('heroicon-m-envelope')->required(),
                Select::make('qrx_code_id') ->relationship( 'qrxCode', 'name')->searchable()->preload()->required(),
                TextInput::make('subject')->required()->columnSpanFull(),
                Textarea::make('body')->required()->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('subject')->label('Subject')->searchable(),
                TextColumn::make('body')->label('Body')->searchable()->limit(50),
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
            'index' => Pages\ListFeatureEmails::route('/'),
            'create' => Pages\CreateFeatureEmail::route('/create'),
            'edit' => Pages\EditFeatureEmail::route('/{record}/edit'),
        ];
    }    
}
