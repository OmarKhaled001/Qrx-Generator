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
                TextInput::make('plan_id')->required(),
                TextInput::make('price')->required(),
                TextInput::make('price_id')->required(),
                TextInput::make('scan_count')->required()->columnSpan(2),
                TextInput::make('qr_count')->required()->columnSpan(2),
                Toggle::make('is_active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')->copyable()->searchable()->copyable(),
                TextColumn::make('scan_count')->label('Scan'),
                TextColumn::make('qr_count')->label('Qr Count'),
                TextColumn::make('plan_id')->isCopyable(),
                TextColumn::make('price_id')->isCopyable(),
                ToggleColumn::make('is_active')->toggleable(isToggledHiddenByDefault: true),
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
