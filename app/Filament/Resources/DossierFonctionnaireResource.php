<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DossierFonctionnaireResource\Pages;
use App\Filament\Resources\DossierFonctionnaireResource\RelationManagers;
use App\Models\DossierFonctionnaire;
use App\Models\Dossier;
use App\Models\SousDossier;
use App\Models\Fonctionnaire;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters;
use Filament\Tables\Actions;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class DossierFonctionnaireResource extends Resource
{
    protected static ?string $model = DossierFonctionnaire::class;

    protected static ?string $navigationLabel = 'Dossiers Fonctionnaire';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Gestion Fonctionnaires';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dossier Fonctionnaire Details')
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
                            ->relationship('fonctionnaire', function ($query) {
                                return $query->select('id', 'nom', 'prenom');
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nom} {$record->prenom}")
                            ->preload()
                            ->searchable(),

                        FileUpload::make('fichier')
                            ->label('Document')
                            ->directory(function ($record, $get) {
                                // Try to get fonctionnaire_id from the form's current state
                                $fonctionnaireId = $get('fonctionnaire_id');
                                
                                if ($fonctionnaireId) {
                                    $fonctionnaire = Fonctionnaire::find($fonctionnaireId);
                                    $folderName = $fonctionnaire 
                                        ? Str::slug("{$fonctionnaire->nom}-{$fonctionnaire->prenom}")
                                        : 'unknown';
                                } else {
                                    $folderName = 'unknown';
                                }
                                
                                $currentDate = now();
                                return "dossier_fonctionnaire_docs/{$folderName}/{$currentDate->year}/{$currentDate->format('m-d')}";
                            })
                            ->maxSize(1024)
                            ->acceptedFileTypes([
                                'application/pdf', 
                                'image/jpeg', 
                                'image/png', 
                                'image/gif', 
                                'application/msword', 
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                            ])
                            ->helperText('Taille de fichier maximale : 1 Mo. Types autorisés : PDF, Images, Documents Word')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fonctionnaire.nom')
                    ->label('Nom')
                    ->searchable(),
                TextColumn::make('fonctionnaire.prenom')
                    ->label('Prénom')
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
