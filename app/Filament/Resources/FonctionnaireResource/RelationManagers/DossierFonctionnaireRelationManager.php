<?php

namespace App\Filament\Resources\FonctionnaireResource\RelationManagers;

use App\Models\Dossier;
use App\Models\SousDossier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DossierFonctionnaireRelationManager extends RelationManager
{
    protected static string $relationship = 'dossierFonctionnaires';

    protected static ?string $title = 'Dossiers';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true; // Ensure the relation manager is always visible
    }

    public function mount(): void
    {
        $this->record = $this->getOwnerRecord();
    }

    public function isTablePaginationEnabled(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dossier_id')
                    ->label('Dossier')
                    ->options(Dossier::pluck('nom_dossier', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('sous_dossier_id', null)),

                Forms\Components\Select::make('sous_dossier_id')
                    ->label('Sous Dossier')
                    ->options(function (callable $get) {
                        $dossierId = $get('dossier_id');
                        return $dossierId 
                            ? SousDossier::where('dossier_id', $dossierId)->pluck('nom_sous_doss', 'id')
                            : [];
                    })
                    ->required(),

                Forms\Components\FileUpload::make('fichier')
                    ->label('Document')
                    ->directory('fonctionnaire_docs')
                    ->maxSize(1024)
                    ->acceptedFileTypes([
                        'application/pdf', 
                        'image/jpeg', 
                        'image/png', 
                        'image/gif', 
                        'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ])
                    ->helperText('Taille de fichier maximale : 1 Mo. Types autorisés : PDF, Images, Documents Word'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),

                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations')
                            ->schema([
                                // Placeholder for now
                                Forms\Components\Placeholder::make('empty_tab')
                                    ->content('Aucune information disponible'),
                            ]),
                    ]),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dossier.nom_dossier')
            ->columns([
                Tables\Columns\TextColumn::make('dossier.nom_dossier')
                    ->label('Dossier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sousDossier.nom_sous_doss')
                    ->label('Sous Dossier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_ajout')
                    ->label('Date Ajout')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter Dossier')
                    ->modalWidth('xl')
                    ->slideOver(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('xl')
                    ->slideOver(false),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
                    ->modalWidth('xl')
                    ->slideOver(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
