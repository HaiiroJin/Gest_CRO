<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JoursFeriesResource\Pages;
use App\Filament\Resources\JoursFeriesResource\RelationManagers;
use App\Models\JoursFeries;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JoursFeriesResource extends Resource
{
    protected static ?string $model = JoursFeries::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Paramètrage';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('date_depart')
                    ->required(),
                Forms\Components\TextInput::make('nombre_jours')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date_fin')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_depart')
                    ->label('Date Début')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_fin')
                    ->label('Date Fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre_jours')
                    ->label('Nombre de Jours')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJoursFeries::route('/'),
        ];
    }
}
