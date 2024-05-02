<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Plan;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PlanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PlanResource\RelationManagers;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required()->columnSpan(2),
                TextInput::make('price')->required(),
                TextInput::make('duration')->suffix('/Month')->required(),
                TextInput::make('scans_count')->required()->columnSpan(2),
                TextInput::make('qrs_count')->required()->columnSpan(2),
                Textarea::make('details')->maxLength(65535)->required()->columnSpan(2),
                Toggle::make('is_active'),
                Toggle::make('is_hidden'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')->copyable()->searchable()->copyable(),
                TextColumn::make('scans_count')->label('Scan'),
                TextColumn::make('qrs_count')->label('Qr Count'),
                TextColumn::make('duration')->label('Duration')->searchable(),
                ToggleColumn::make('is_active')->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_hidden')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }    
}
