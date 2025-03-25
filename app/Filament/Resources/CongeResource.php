<?php

namespace App\Filament\Resources;

use App\Models\Conge;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CongeResource extends Resource
{
    // Modèle associé à cette ressource
    public static ?string $model = Conge::class;

    // Configuration de la navigation dans le panneau d'administration
    public static ?string $navigationIcon = 'heroicon-o-document-text';
    public static ?string $navigationLabel = 'Congés';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('super_admin') ? 'Gestion des Demandes' : 'Demandes';
    }
    public static ?int $navigationSort = 2;

    // Méthode utilitaire pour obtenir le fonctionnaire sélectionné
    private static function getSelectedFonctionnaire($get = null): ?\App\Models\Fonctionnaire
    {
        // For super admin, prioritize form input, then request input
        if (auth()->user()->hasRole('super_admin')) {
            $fonctionnaireId = null;
            
            // Try to get from form get method
            if ($get) {
                $fonctionnaireId = $get('fonctionnaire_id');
            }
            
            // If not found in form, try from request
            if (!$fonctionnaireId) {
                $fonctionnaireId = request()->input('fonctionnaire_id');
            }
        } else {
            // For regular users, use their own fonctionnaire_id
            $fonctionnaireId = auth()->user()->fonctionnaire_id;
        }
        
        return $fonctionnaireId ? \App\Models\Fonctionnaire::find($fonctionnaireId) : null;
    }

    // Méthode de validation personnalisée pour le nombre de jours
    private static function validateSoldeConge($value, $fail)
    {
        // For super admin, prioritize form input, then request input
        if (auth()->user()->hasRole('super_admin')) {
            $fonctionnaireId = request()->input('fonctionnaire_id');
            $congeType = request()->input('type');
        } else {
            $fonctionnaireId = auth()->user()->fonctionnaire_id;
            $congeType = request()->input('type');
        }
        
        // Ensure fonctionnaire ID is valid
        if (!$fonctionnaireId) {
            $fail("Aucun fonctionnaire sélectionné.");
            return;
        }

        $fonctionnaire = \App\Models\Fonctionnaire::findOrFail($fonctionnaireId);

        // Only validate for 'annuel' type leaves
        if ($congeType === 'annuel') {
            // Validate that value is a positive number
            if (!is_numeric($value) || $value <= 0) {
                $fail("Le nombre de jours doit être un nombre positif.");
                return;
            }

            // Recalculate solde conge at the moment of validation
            $currentSoldeConge = $fonctionnaire->calculateCurrentSoldeConge();

            // Check solde
            if ($value > $currentSoldeConge) {
                $fail("Vous ne pouvez pas demander {$value} jours. Votre solde de congé est de {$currentSoldeConge} jours.");
                return;
            }
        }
    }

    // Méthode pour générer le texte d'aide pour le solde de congé
    private static function getLeaveBalanceHelperText($get): string
    {
        // For super admin, prioritize form input, then request input
        if (auth()->user()->hasRole('super_admin')) {
            $fonctionnaireId = null;
            
            // Try to get from form get method
            if ($get) {
                $fonctionnaireId = $get('fonctionnaire_id');
            }
            
            // If not found in form, try from request
            if (!$fonctionnaireId) {
                $fonctionnaireId = request()->input('fonctionnaire_id');
            }
            
            // Get the type of leave
            $congeType = $get ? $get('type') : request()->input('type');
        } else {
            // For regular users, use their own fonctionnaire_id
            $fonctionnaireId = auth()->user()->fonctionnaire_id;
            $congeType = $get ? $get('type') : request()->input('type');
        }
        
        $fonctionnaire = $fonctionnaireId 
            ? \App\Models\Fonctionnaire::find($fonctionnaireId) 
            : null;
        
        // Show balance for 'annuel' type leaves
        return $fonctionnaire && $congeType === 'annuel'
            ? "Solde de congé disponible : {$fonctionnaire->solde_congé} jours" 
            : (auth()->user()->hasRole('super_admin') 
                ? "Sélectionnez un fonctionnaire pour voir son solde" 
                : "");
    }

    // Formulaire de création et d'édition d'un congé
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Fonctionnaire selection for super admin
                \Filament\Forms\Components\Select::make('fonctionnaire_id')
                    ->label('Fonctionnaire')
                    ->options(function () {
                        if (auth()->user()->hasRole('super_admin')) {
                            return \App\Models\Fonctionnaire::all()
                                ->mapWithKeys(function ($fonctionnaire) {
                                    $fullName = trim($fonctionnaire->nom . ' ' . $fonctionnaire->prenom);
                                    $displayName = $fullName ?: "Fonctionnaire #" . $fonctionnaire->id;
                                    return [$fonctionnaire->id => $displayName];
                                })
                                ->toArray();
                        }
                        
                        $user = auth()->user();
                        $fullName = trim($user->fonctionnaire->nom . ' ' . $user->fonctionnaire->prenom);
                        $displayName = $fullName ?: "Fonctionnaire #" . $user->fonctionnaire_id;
                        return [$user->fonctionnaire_id => $displayName];
                    })
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                        $set('fonctionnaire_id', $state);
                    })
                    ->hidden(fn() => !auth()->user()->hasRole('super_admin'))
                    // Ensure that for super admins, the default is null (to allow selection)
                    ->default(fn() => auth()->user()->hasRole('super_admin') 
                        ? null 
                        : auth()->user()->fonctionnaire_id)
                    ->columnSpan('full')
                    ->searchable(),


                // Date de la demande (automatiquement définie)
                \Filament\Forms\Components\DatePicker::make('date_demande')
                    ->label('Date de la demande')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->format('Y-m-d')
                    ->columnSpan('full'),

                // Sélection du type de congé
                \Filament\Forms\Components\Radio::make('type')
                    ->label('Type de congé')
                    ->options([
                        'annuel' => 'Congé annuel',
                        'exceptionnel' => 'Congé exceptionnel',
                    ])
                    ->columns(2)
                    ->inline()
                    ->default('annuel')
                    ->required()
                    ->columnSpan('full'),

                // Date de départ
                \Filament\Forms\Components\DatePicker::make('date_depart')
                    ->label('Date de départ')
                    ->required()
                    ->default(fn() => now()->format('Y-m-d'))
                    ->dehydrated(true)
                    ->live()
                    ->afterOrEqual(now()->startOfDay())
                    ->afterOrEqual(fn(\Filament\Forms\Get $get) => 
                        self::getSelectedFonctionnaire($get)?->last_conge_date ?? now()->startOfDay())
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, $state) {
                        $startDate = Carbon::parse($state);
                        $numberOfDays = 1; // Default to 1 day
                        $returnDate = self::calculateReturnDate($startDate, $numberOfDays);
                        $set('date_retour', $returnDate->format('Y-m-d'));
                    }),

                // Nombre de jours de congé
                \Filament\Forms\Components\TextInput::make('nombre_jours')
                    ->label('Nombre de jours')
                    ->required()
                    ->numeric()
                    ->helperText(fn(\Filament\Forms\Get $get) => self::getLeaveBalanceHelperText($get))
                    ->live()
                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get, $state) {
                        $startDate = Carbon::parse($get('date_depart'));
                        
                        // If no days are input, set return date same as start date
                        if (!$state) {
                            $set('date_retour', $startDate->format('Y-m-d'));
                            return;
                        }
                        
                        // Validate and convert input to number
                        $numberOfDays = max(1, (int)$state);
                        
                        $returnDate = self::calculateReturnDate($startDate, $numberOfDays);
                        $set('date_retour', $returnDate->format('Y-m-d'));
                    })
                    ->rules([
                        fn() => fn($attribute, $value, $fail) => self::validateSoldeConge($value, $fail)
                    ])
                    ->validationMessages([
                        'rules' => function(\Filament\Forms\Get $get) {
                            // For 'annuel' type, check solde
                            if ($get('type') === 'annuel') {
                                $fonctionnaireId = auth()->user()->hasRole('super_admin') 
                                    ? $get('fonctionnaire_id') 
                                    : auth()->user()->fonctionnaire_id;
                                
                                $fonctionnaire = \App\Models\Fonctionnaire::find($fonctionnaireId);
                                
                                if ($fonctionnaire) {
                                    $requestedDays = $get('nombre_jours');
                                    return "Vous ne pouvez pas demander {$requestedDays} jours. Votre solde de congé est de {$fonctionnaire->solde_congé} jours.";
                                }
                            }
                            
                            return "Nombre de jours invalide.";
                        }
                    ])
                    ->validationAttribute('nombre de jours'),

                // Date de retour
                \Filament\Forms\Components\DatePicker::make('date_retour')
                    ->label('Date de retour')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->live(),

                // Autorisation de sortie du territoire
                \Filament\Forms\Components\Checkbox::make('autorisation_sortie_territoire')
                    ->label('Autorisation de sortie du territoire')
                    ->default(false),
            ])
            ->columns(2);
    }

    // Override record creation to ensure correct fonctionnaire and validate solde
    public function handleRecordCreation(array $data): Model
    {
        // Determine fonctionnaire ID
        $fonctionnaireId = auth()->user()->hasRole('super_admin') 
            ? $data['fonctionnaire_id'] 
            : auth()->user()->fonctionnaire_id;
        $data['fonctionnaire_id'] = $fonctionnaireId;

        // Additional validation for 'annuel' type leaves
        if ($data['type'] === 'annuel') {
            $fonctionnaire = \App\Models\Fonctionnaire::findOrFail($fonctionnaireId);

            // Validate leave balance
            $currentSoldeConge = $fonctionnaire->calculateCurrentSoldeConge();
            
            if ($data['nombre_jours'] > $currentSoldeConge) {
                throw new \Exception("Vous ne pouvez pas demander {$data['nombre_jours']} jours. Votre solde de congé est de {$currentSoldeConge} jours.");
            }
        }

        return static::getModel()::create($data);
    }

    public function handleRecordUpdate(Model $record, array $data): Model
    {
        $fonctionnaireId = auth()->user()->hasRole('super_admin') ? $data['fonctionnaire_id'] : auth()->user()->fonctionnaire_id;
        $data['fonctionnaire_id'] = $fonctionnaireId;

        $record->update($data);

        return $record;
    }


    // Configuration du tableau des congés
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Colonnes affichées dans le tableau
                TextColumn::make('fonctionnaire.nom')
                    ->label('Nom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fonctionnaire.prenom')
                    ->label('Prénom')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->label('Statut')
                    ->sortable()
                    ->color(fn($state) => match($state){
                        'en cours' => 'primary',
                        'signée' => 'success',
                        'rejetée' => 'danger',
                        'demande_annulation' => 'warning',
                        default => 'gray'
                    }),

                TextColumn::make('nombre_jours')
                    ->label('Nombre de jours')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('type')
                    ->label('Type de congé')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date_demande')
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('date_depart')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('date_retour')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtres pour le tableau
                SelectFilter::make('status')
                    ->label('Filtrer par statut')
                    ->options([
                        'en cours' => 'En cours',
                        'signée' => 'Signée',
                        'rejetée' => 'Rejetée',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
                // Actions disponibles pour chaque ligne
                Action::make('approve')
                    ->label('Signer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->status === 'en cours' && $record->deleted_at === null)
                    ->action(fn ($record) => self::approuverDemande($record)),

                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->status === 'en cours' && $record->deleted_at === null)
                    ->action(fn ($record) => self::rejeterDemande($record)),

                Action::make('download_demande')
                    ->label('Demande')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn ($record) => $record->deleted_at === null)
                    ->url(fn ($record) => route('conge.demande', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('download_avis_retour')
                    ->label('Avis de Retour')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn($record) => route('conge.avis_retour', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->status === 'signée' && $record->deleted_at === null),

                Action::make('download_decision')
                    ->label('Décision')
                    ->icon('heroicon-o-document')
                    ->color('primary')
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->deleted_at === null)
                    ->url(fn ($record) => route('conge.decision', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('cancel')
                    ->label("Annuler")
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->deleted_at === null)
                    ->action(fn ($record) => self::supprimerConge($record)),

                Action::make('request_cancel')
                    ->label("Demander l'annulation")
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmer la demande d\'annulation')
                    ->modalDescription('Êtes-vous absolument certain de vouloir demander l\'annulation de cette demande de congé ? Cette action est irréversible et nécessite l\'approbation d\'un administrateur. Une fois soumise, vous ne pourrez pas annuler cette demande.')
                    ->modalSubmitActionLabel('Oui, demander l\'annulation')
                    ->modalCancelActionLabel('Annuler')
                    ->visible(fn ($record) => !auth()->user()->hasRole('super_admin') && $record->status === 'en cours' && $record->deleted_at === null)
                    ->action(function ($record) {
                        // Update the record status to indicate cancellation request
                        $record->update([
                            'status' => 'demande_annulation'
                        ]);
                        
                        // Optional: Add notification or logging
                        \Filament\Notifications\Notification::make()
                            ->title('Demande d\'annulation envoyée')
                            ->body('Votre demande d\'annulation a été soumise et sera traitée par un administrateur.')
                            ->success()
                            ->send();
                    }),

                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_demande', 'desc')
            ->persistFiltersInSession()
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    // Méthode pour obtenir la requête Eloquent avec filtrage par rôle
    public static function getEloquentQuery(): Builder
    {
        return static::filterByUserRole(Conge::query());
    }

    // Méthode de routage des pages
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CongeResource\Pages\ManageCongees::route('/'),
        ];
    }

    // Filtrer les requêtes en fonction du rôle de l'utilisateur
    public static function filterByUserRole(Builder $query): Builder
    {
        return $query->when(
            auth()->check() && !auth()->user()->hasRole('super_admin'), 
            fn($q) => $q->where('fonctionnaire_id', auth()->user()->fonctionnaire_id)
        );
    }

    // Préparer les données avant la création d'un congé
    public function prepareLeaveData(array $data): array
    {
        $fonctionnaireId = $data['fonctionnaire_id'];
        $fonctionnaire = $this->findFonctionnaire($fonctionnaireId);
        $user = $this->findAssociatedUser($fonctionnaireId);

        return [
            ...$data,
            'user_id' => $user?->id,
        ];
    }

    // Trouver le fonctionnaire par son ID
    public function findFonctionnaire(int $fonctionnaireId): \App\Models\Fonctionnaire
    {
        return \App\Models\Fonctionnaire::findOrFail($fonctionnaireId);
    }

    // Trouver l'utilisateur associé à un fonctionnaire
    public function findAssociatedUser(int $fonctionnaireId): ?\App\Models\User
    {
        return \App\Models\User::whereHas('fonctionnaire', 
            fn($query) => $query->where('id', $fonctionnaireId)
        )->first();
    }

    // Hook avant la création pour valider le solde de congé
    public static function beforeCreation(Conge $record): Conge
    {
        // Determine the fonctionnaire
        $fonctionnaireId = auth()->user()->hasRole('super_admin') 
            ? $record->fonctionnaire_id 
            : auth()->user()->fonctionnaire_id;
        
        $fonctionnaire = \App\Models\Fonctionnaire::find($fonctionnaireId);
        
        // Validate leave balance
        if (!$fonctionnaire) {
            throw new \Exception("No fonctionnaire selected.");
        }
        
        $totalLeaveBalance = ($fonctionnaire->previous_year_balance ?? 0) + ($fonctionnaire->current_year_balance ?? 0);
        
        if ($totalLeaveBalance < $record->nombre_jours) {
            throw new \Exception("The fonctionnaire does not have enough leave days available. Current balance: {$totalLeaveBalance} days.");
        }

        // Déduire les jours de congé
        // Priorité à solde_année_prec, puis solde_année_act
        if ($fonctionnaire->previous_year_balance >= $record->nombre_jours) {
            $fonctionnaire->previous_year_balance -= $record->nombre_jours;
        } else {
            $remainingDays = $record->nombre_jours - $fonctionnaire->previous_year_balance;
            $fonctionnaire->previous_year_balance = 0;
            $fonctionnaire->current_year_balance -= $remainingDays;
        }
        
        $fonctionnaire->save();

        return $record;
    }

    // Requête de table personnalisée pour filtrer selon le rôle
    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->when(
                auth()->user()->hasRole('super_admin'), 
                fn($query) => $query, 
                fn($query) => $query->where('fonctionnaire_id', auth()->user()->fonctionnaire_id)
            );
    }

    // Méthode pour approuver une demande de congé
    public static function approuverDemande(Conge $record)
    {
        // Récupérer le fonctionnaire associé à la demande
        $fonctionnaire = \App\Models\Fonctionnaire::find($record->fonctionnaire_id);

        // Calculer le solde total
        $totalLeaveBalance = ($fonctionnaire->solde_année_prec ?? 0) + ($fonctionnaire->solde_année_act ?? 0);

        // Vérifier si le fonctionnaire a suffisamment de solde
        if ($totalLeaveBalance < $record->nombre_jours) {
            throw new \Exception("Insufficient leave balance.");
        }

        // Mettre à jour le statut
        $record->update(['status' => 'signée']);

        // Déduire les jours de congé
        // Priorité à solde_année_prec, puis solde_année_act
        if ($fonctionnaire->solde_année_prec >= $record->nombre_jours) {
            $fonctionnaire->solde_année_prec -= $record->nombre_jours;
        } else {
            $remainingDays = $record->nombre_jours - $fonctionnaire->solde_année_prec;
            $fonctionnaire->solde_année_prec = 0;
            $fonctionnaire->solde_année_act -= $remainingDays;
        }
        
        $fonctionnaire->save();

        return $record;
    }

    // Méthode pour rejeter une demande de congé
    public static function rejeterDemande(Model $record)
    {
        $record->update([
            'status' => 'rejetée',
        ]);
    }

    // Méthode personnalisée pour supprimer un congé
    public static function supprimerConge(Conge $record): Conge
    {
        // Vérifier les permissions de suppression
        $user = auth()->user();
        
        // Si l'utilisateur n'est pas admin, vérifier que le congé n'est pas signé
        if (!$user->hasRole('super_admin') && $record->status === 'signée') {
            throw new \Exception("You are not authorized to delete this leave.");
        }

        // Restituer les jours de congé si le congé était signé et de type annuel
        if ($record->status === 'signée' && $record->type === 'annuel') {
            $fonctionnaire = $record->user->fonctionnaire;
            $numberOfDays = $record->nombre_jours;

            // Restitution des jours de congé
            // Ajouter d'abord à l'année en cours jusqu'à 22 jours
            $remainingDaysToAdd = $numberOfDays;
            $maxCurrentYearDays = 22;

            // Calculer combien de jours peuvent être ajoutés à l'année en cours
            $currentYearSpaceLeft = $maxCurrentYearDays - $fonctionnaire->solde_année_act;
            
            if ($currentYearSpaceLeft > 0) {
                // Ajouter des jours à l'année en cours
                $daysToAddCurrentYear = min($currentYearSpaceLeft, $remainingDaysToAdd);
                $fonctionnaire->solde_année_act += $daysToAddCurrentYear;
                $remainingDaysToAdd -= $daysToAddCurrentYear;
            }

            // Si des jours restent, les ajouter à l'année précédente
            if ($remainingDaysToAdd > 0) {
                $fonctionnaire->solde_année_prec += $remainingDaysToAdd;
            }
            
            $fonctionnaire->save();
        }

        // Supprimer l'enregistrement
        $record->delete();

        return $record;
    }

    // Méthode pour calculer la date de retour en excluant les week-ends et jours fériés
    public static function calculateReturnDate(Carbon $startDate, int $numberOfDays): Carbon
    {
        $returnDate = clone $startDate;
        $daysAdded = 0;

        // Récupérer tous les jours fériés pour l'année en cours
        $holidays = \App\Models\JoursFeries::query()
            ->whereYear('date_depart', $returnDate->year)
            ->get()
            ->flatMap(function($holiday) {
                // Générer toutes les dates entre date_depart et date_fin
                $dates = [];
                $currentDate = Carbon::parse($holiday->date_depart);
                $endDate = Carbon::parse($holiday->date_fin);

                while ($currentDate->lte($endDate)) {
                    $dates[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                }

                return $dates;
            })
            ->unique()
            ->toArray();

        while ($daysAdded < $numberOfDays) {
            $returnDate->addDay();

            // Vérifier si c'est un week-end (samedi ou dimanche)
            if ($returnDate->isWeekend()) {
                continue;
            }

            // Vérifier si c'est un jour férié
            if (in_array($returnDate->format('Y-m-d'), $holidays)) {
                continue;
            }

            $daysAdded++;
        }

        return $returnDate;
    }

    

    public static function boot()
    {
        parent::boot();
    }
}
