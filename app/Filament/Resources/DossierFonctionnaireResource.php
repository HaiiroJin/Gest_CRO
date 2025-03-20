<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DossierFonctionnaireResource\Pages;
use App\Filament\Resources\DossierFonctionnaireResource\RelationManagers;
use App\Models\DossierFonctionnaire;
use App\Models\Dossier;
use App\Models\SousDossier;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DossierFonctionnaireResource extends Resource
{
    protected static ?string $model = DossierFonctionnaire::class;

    protected static ?string $navigationLabel = 'Dossiers Fonctionnaire';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('dossier_id')
                    ->label('Dossier')
                    ->options(Dossier::getDossiers())
                    ->searchable()
                    ->required(),

                Select::make('sous_dossier_id')
                    ->label('Sous Dossier')
                    ->options(SousDossier::getSousDossiers())
                    ->searchable()
                    ->required(),
                Select::make('fonctionnaire_id')
                    ->label('Fonctionnaire')
                    ->relationship('fonctionnaire', 'nom')
                    ->preload()
                    ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fonctionnaire.nom')
                    ->label('Fonctionnaire')
                    ->searchable(),
                TextColumn::make('dossier.nom_dossier')
                    ->label('Dossier')
                    ->searchable(),
                TextColumn::make('sous_dossier.nom_sous_doss')
                    ->label('Sous Dossier')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
                Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDossierFonctionnaire::route('/'),
        ];
    }
}
