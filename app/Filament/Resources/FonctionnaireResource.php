<?php

namespace App\Filament\Resources;

use App\Models\Fonctionnaire;
use Filament\Forms\Form;
use Filament\Forms;
use App\Filament\Resources\FonctionnaireResource\Pages\ManageFonctionnaires;
use App\Filament\Resources\FonctionnaireResource\RelationManagers\DossierFonctionnaireRelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FonctionnaireResource extends Resource
{
    protected static ?string $model = Fonctionnaire::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestion Fonctionnaires';

    protected static ?int $navigationSort = 1;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Fonctionnaire')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informations Personnelles')
                            ->schema([
                                Forms\Components\TextInput::make('nom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('prenom')
                                    ->label('Prénom')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('nom_ar')
                                    ->label('Nom (Arabe)')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('prenom_ar')
                                    ->label('Prénom (Arabe)')
                                    ->maxLength(255),
                                Forms\Components\Radio::make('civilite')
                                    ->label('Civilité')
                                    ->options([
                                        'M' => 'M',
                                        'Mme' => 'Mme',
                                    ])
                                    ->columns(2),
                                Forms\Components\DatePicker::make('date_naissance')
                                    ->required(),
                                Forms\Components\TextInput::make('cin')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tel')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('rib')
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255)
                                    ->columnSpan(2),
                                Forms\Components\Textarea::make('adresse')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(4),
                        
                        Forms\Components\Tabs\Tab::make('Situation administrative')
                            ->schema([
                                Forms\Components\Select::make('direction_id')
                                    ->options(\App\Models\Direction::getDirectionsOptions())
                                    ->searchable()
                                    ->label('Direction'),
                                Forms\Components\Select::make('division_id')
                                    ->options(\App\Models\Division::getDivisionsOptions())
                                    ->searchable()
                                    ->label('Division'),
                                Forms\Components\Select::make('service_id')
                                    ->options(\App\Models\Service::getServicesOptions())
                                    ->searchable()
                                    ->label('Service'),
                                Forms\Components\Select::make('groupe_id')
                                    ->options(\App\Models\Groupe::getOptions())
                                    ->searchable()
                                    ->label('Groupe'),
                                Forms\Components\Select::make('grade_id')
                                    ->options(\App\Models\Grade::getOptions())
                                    ->searchable()
                                    ->label('Grade'),
                                Forms\Components\Select::make('corps_id')
                                    ->options(\App\Models\Corps::getOptions())
                                    ->searchable()
                                    ->label('Corps'),
                                Forms\Components\TextInput::make('poste')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('situation')
                                    ->maxLength(255),
                                Forms\Components\DatePicker::make('date_recruitement')
                                    ->required(),
                                Forms\Components\TextInput::make('date_affectation_cro')
                                    ->required(),
                                Forms\Components\TextInput::make('matricule_aujour')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Solde')
                            ->schema([
                                Forms\Components\TextInput::make('solde_année_prec')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('solde_année_act')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Dossiers')
                            ->schema([
                                Forms\Components\Repeater::make('dossierFonctionnaires')
                                    ->label('Dossiers')
                                    ->relationship('dossierFonctionnaires')
                                    ->schema([
                                        Forms\Components\Select::make('dossier_id')
                                            ->label('Dossier')
                                            ->options(\App\Models\Dossier::getDossiers())
                                            ->searchable()
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $set) => $set('sous_dossier_id', null)),
                                        Forms\Components\Select::make('sous_dossier_id')
                                            ->label('Sous Dossier')
                                            ->options(\App\Models\SousDossier::getSousDossiers())
                                            ->searchable(),
                                        Forms\Components\FileUpload::make('fichier')
                                            ->label('Document')
                                            ->directory('dossier_fonctionnaire/temp')
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
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('Ajouter un Dossier')
                                    ->deleteAction(
                                        fn (Forms\Components\Actions\Action $action) => $action->requiresConfirmation()
                                    )
                            ]),
                    ])
                    ->activeTab(1),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('matricule_aujour')
                    ->label('Matricule')
                    ->searchable(),
                Tables\Columns\TextColumn::make('corps.libelle')
                    ->label('Corps')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade.libelle')
                    ->label('Grade')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Fonctionnaire')
                    ->tabs([
                        Tabs\Tab::make('Informations Personnelles')
                            ->schema([
                                TextEntry::make('nom')
                                    ->label('Nom'),
                                TextEntry::make('prenom')
                                    ->label('Prénom'),
                                TextEntry::make('nom_ar')
                                    ->label('Nom arabe'),
                                TextEntry::make('prenom_ar')
                                    ->label('Prénom arabe'),
                                TextEntry::make('civilite')
                                    ->label('Civilité'),
                                TextEntry::make('date_naissance')
                                    ->label('Date naissance'),
                                TextEntry::make('cin')
                                    ->label('CIN'),
                                TextEntry::make('rib')
                                    ->label('RIB'),
                                TextEntry::make('tel')
                                    ->label('Téléphone'),
                                TextEntry::make('email')
                                    ->label('Email'),
                                TextEntry::make('adresse')
                                    ->label('Adresse')
                                    ->columnSpan(2),
                            ])
                            ->columns(4),
                        Tabs\Tab::make('Situation administrative')
                            ->schema([
                                TextEntry::make('direction.libelle')
                                    ->label('Direction'),
                                TextEntry::make('division.libelle')
                                    ->label('Division'),
                                TextEntry::make('service.libelle')
                                    ->label('Service'),
                                TextEntry::make('corps.libelle')
                                    ->label('Corps'),
                                TextEntry::make('grade.libelle')
                                    ->label('Grade'),
                                TextEntry::make('groupe.libelle')
                                    ->label('Groupe'),
                                TextEntry::make('date_recruitement')
                                    ->label('Date recruitement'),
                                TextEntry::make('date_affectation_cro')
                                    ->label('Date affectation cro'),
                                TextEntry::make('matricule_aujour')
                                    ->label('Matricule aujour'),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Solde')
                            ->schema([
                                TextEntry::make('solde_année_prec')
                                    ->label('Solde année préc'),
                                TextEntry::make('solde_année_act')
                                    ->label('Solde année act'),
                                TextEntry::make('solde_congé')
                                    ->label('Solde congé'),
                            ])
                            ->columns(3),
                        Tabs\Tab::make('Dossiers')
                            ->schema([
                                TextEntry::make('dossierFonctionnaires')
                                    ->label('Dossiers')
                                    ->listWithLineBreaks()
                                    ->getStateUsing(function ($record) {
                                        return $record->dossierFonctionnaires->map(function ($dossier) {
                                            $details = [];
                                            
                                            // Dossier
                                            $details[] = "Dossier: {$dossier->dossier->nom_dossier}";
                                            
                                            // Sous Dossier (if exists)
                                            if ($dossier->sousDossier) {
                                                $details[] = "Sous Dossier: {$dossier->sousDossier->nom_sous_doss}";
                                            }
                                            
                                            // Description (if exists)
                                            if ($dossier->description) {
                                                $details[] = "Description: {$dossier->description}";
                                            }
                                            
                                            // File (if exists)
                                            if ($dossier->fichier) {
                                                $filename = basename($dossier->fichier);
                                                $details[] = "Document: " . 
                                                    "<a href='" . route('dossier.view', ['id' => $dossier->id]) . "' target='_blank' class='text-primary-600 hover:underline'>{$filename}</a>";
                                            }
                                            
                                            return implode(" | ", $details);
                                        })->toArray();
                                    })
                                    ->html()
                            ])
                            ->columns(1),
                    ])
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFonctionnaires::route('/'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            DossierFonctionnaireRelationManager::class,
        ];
    }
}
