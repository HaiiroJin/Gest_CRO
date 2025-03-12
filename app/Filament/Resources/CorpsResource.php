<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorpsResource\Pages;
use App\Filament\Resources\CorpsResource\RelationManagers;
use App\Models\Corps;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorpsResource extends Resource
{
    protected static ?string $model = Corps::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Structure Organisationnelle';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('libelle')
                    ->required()
                    ->maxLength(250),
                Forms\Components\TextInput::make('libelle_ar')
                    ->required()
                    ->maxLength(250),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('libelle')
                    ->searchable(),
                Tables\Columns\TextColumn::make('libelle_ar')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCorps::route('/'),
        ];
    }
}
