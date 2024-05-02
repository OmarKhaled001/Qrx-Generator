<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\QrxCode;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\QrxCodeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\QrxCodeResource\RelationManagers;

class QrxCodeResource extends Resource
{
    protected static ?string $model = QrxCode::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Qr Code';
    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('scan_count')->required(),
                Select::make('user_id') ->relationship('user', 'name')->searchable()->preload()->required(),
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
                TextColumn::make('user.name')->searchable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListQrxCodes::route('/'),
            'create' => Pages\CreateQrxCode::route('/create'),
            'edit' => Pages\EditQrxCode::route('/{record}/edit'),
        ];
    }    
}
