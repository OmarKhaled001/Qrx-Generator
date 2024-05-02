<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class QrxCodesRelationManager extends RelationManager
{
    protected static string $relationship = 'qrxCodes';

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('scan_count')->required(),
                Select::make('client_id') ->relationship('client', 'name')->searchable()->preload()->required(),
                Select::make('folder_id') ->relationship('folder', 'name')->searchable()->preload(),
                Radio::make('status')
                ->options([
                    'active' => 'Active',
                    'pause' => 'Pause',
                    'exp' => 'Exp',
                ])->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->copyable()->searchable(),
                TextColumn::make('scan_count')->label('Scan'),
                TextColumn::make('code')->label('Short Url')->copyable()->searchable(),
                TextColumn::make('client.name')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('folder.name')->searchable()->placeholder('No folder.')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type')->searchable(),
                BadgeColumn::make('status')->searchable()
                ->colors([
                    'success '=> 'active',
                    'warning'=>  'pause' ,
                    'danger '=>  'exp',
            ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    
}
