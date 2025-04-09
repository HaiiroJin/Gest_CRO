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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Fonctionnaire;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;

class CongeResource extends Resource
{
    // Modèle associé à cette ressource
    public static ?string $model = Conge::class;

    // Configuration de la navigation dans le panneau d'administration
    public static ?string $navigationIcon = 'heroicon-o-document-text';
    public static ?string $navigationLabel = 'Congés';
    public static function getNavigationGroup(): ?string
    {
        return Gate::allows('manage-conges') ? 'Gestion des Demandes' : 'Demandes';
    }
    public static ?int $navigationSort = 2;

    // Formulaire de création et d'édition d'un congé
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Fonctionnaire selection for super admin
                Forms\Components\Select::make('fonctionnaire_id')
                    ->label('Fonctionnaire')
                    ->options(function () {
                        if (Gate::allows('manage-conges')) {
                            return Fonctionnaire::all()
                                ->mapWithKeys(function ($fonctionnaire) {
                                    $fullName = trim($fonctionnaire->nom . ' ' . $fonctionnaire->prenom);
                                    $displayName = $fullName ?: "Fonctionnaire #" . $fonctionnaire->id;
                                    return [$fonctionnaire->id => $displayName];
                                })
                                ->toArray();
                        }
                        
                        $user = Filament::auth()->user();
                        if ($user->fonctionnaire) {
                            $fullName = trim($user->fonctionnaire->nom . ' ' . $user->fonctionnaire->prenom);
                            $displayName = $fullName ?: "Fonctionnaire #" . $user->fonctionnaire_id;
                            return [$user->fonctionnaire_id => $displayName];
                        }
                        return [];
                    })
                    //->required()
                    ->live()
                    ->visible(fn() => Gate::allows('manage-conges'))
                    ->default(fn() => Gate::allows('manage-conges') 
                        ? null 
                        : Filament::auth()->user()->fonctionnaire_id)
                    ->searchable(),

                // Date de la demande (automatiquement définie)
                Forms\Components\DatePicker::make('date_demande')
                    ->label('Date de la demande')
                    ->default(now())
                    ->disabled()
                    ->dehydrated(true)
                    ->required()
                    ->format('Y-m-d'),

                // Type de congé
                Forms\Components\Radio::make('type')
                    ->label('Type de congé')
                    ->options([
                        'annuel' => 'Congé annuel',
                        'exceptionnel' => 'Congé exceptionnel',
                    ])
                    ->inline()
                    ->default('annuel')
                    ->required()
                    ->live(),

                // Date de départ
                Forms\Components\DatePicker::make('date_depart')
                    ->label('Date de départ')
                    ->required()
                    ->default(now())
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        if (!$state) return;

                        // Get nombre_jours if set
                        $nombreJours = $get('nombre_jours');
                        if (!$nombreJours) return;

                        // Calculate return date
                        $startDate = Carbon::parse($state);
                        $returnDate = self::calculateReturnDate($startDate, (int)$nombreJours);
                        $set('date_retour', $returnDate->format('Y-m-d'));
                    })
                    ->rules([
                        function () {
                            return function ($attribute, $value, $fail) {
                                $newStartDate = Carbon::parse($value);
                                
                                // Get fonctionnaire ID
                                $fonctionnaireId = Gate::allows('manage-conges')
                                    ? request('data.fonctionnaire_id')
                                    : Filament::auth()->user()->fonctionnaire_id;

                                if (!$fonctionnaireId) return;

                                // Check for existing congé with future return date
                                $latestConge = Conge::where('fonctionnaire_id', $fonctionnaireId)
                                    ->orderBy('date_retour', 'desc')
                                    ->first();

                                if ($latestConge) {
                                    $lastReturnDate = Carbon::parse($latestConge->date_retour);
                                    if ($newStartDate->lessThanOrEqualTo($lastReturnDate)) {
                                        $fail("La date de départ doit être après la date de retour du congé précédent (" . $lastReturnDate->format('d/m/Y') . ").");
                                    }
                                }
                            };
                        }
                    ]),

                // Nombre de jours
                Forms\Components\TextInput::make('nombre_jours')
                    ->label('Nombre de jours')
                    ->required()
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        if (!$state) return;

                        $date_depart = Carbon::parse($get('date_depart'));
                        $nombreJours = max(1, (int)$state);
                        $dateRetour = self::calculateReturnDate($date_depart, $nombreJours);
                        $set('date_retour', $dateRetour->format('Y-m-d'));
                    })
                    ->helperText(function (Forms\Get $get) {

                        $fonctionnaireId = Gate::allows('manage-conges')
                            ? $get('fonctionnaire_id')
                            : Filament::auth()->user()->fonctionnaire_id;

                        if (!$fonctionnaireId) {
                            return Gate::allows('manage-conges') 
                                ? "Sélectionnez un fonctionnaire pour voir son solde" 
                                : "";
                        }

                        $fonctionnaire = Fonctionnaire::find($fonctionnaireId);
                        if (!$fonctionnaire) {
                            return "";
                        }

                        $solde = ($fonctionnaire->solde_année_prec + $fonctionnaire->solde_année_act) ?? 22;
                        return "Solde de congé disponible : {$solde} jours";
                    })
                    ->disabled(function (Forms\Get $get) {
                        if ($get('type') !== 'annuel') {
                            return false;
                        }
                        
                        $fonctionnaireId = Gate::allows('manage-conges')
                            ? $get('fonctionnaire_id')
                            : Filament::auth()->user()->fonctionnaire_id;
                            
                        if (!$fonctionnaireId) {
                            return false;
                        }
                        
                        $fonctionnaire = Fonctionnaire::find($fonctionnaireId);
                        if (!$fonctionnaire) {
                            return false;
                        }
                        
                        $solde = $fonctionnaire->solde_année_act ?? 22;
                        return $solde <= 0;
                    })
                    ->dehydrated(function (Forms\Get $get, $state) {
                        // Prevent form submission if nombre_jours exceeds solde
                        if ($get('type') === 'annuel' && $state) {
                            $fonctionnaireId = Gate::allows('manage-conges')
                                ? $get('fonctionnaire_id')
                                : Filament::auth()->user()->fonctionnaire_id;

                            if ($fonctionnaireId) {
                                $fonctionnaire = Fonctionnaire::find($fonctionnaireId);
                                if ($fonctionnaire) {
                                    $solde = $fonctionnaire->solde_année_act ?? 22;
                                    if ((int)$state > $solde) {
                                        return false;
                                    }
                                }
                            }
                        }
                        return true;
                    })
                    ->rules([
                        function () {
                            return function ($attribute, $value, $fail) {
                                // Only validate for 'annuel' type leaves
                                if (request('data.type') === 'annuel') {
                                    // Get fonctionnaire
                                    $fonctionnaireId = Gate::allows('manage-conges')
                                        ? request('data.fonctionnaire_id')
                                        : Filament::auth()->user()->fonctionnaire_id;

                                    if (!$fonctionnaireId) {
                                        $fail("Aucun fonctionnaire sélectionné.");
                                        return;
                                    }

                                    $fonctionnaire = Fonctionnaire::find($fonctionnaireId);
                                    if (!$fonctionnaire) {
                                        $fail("Fonctionnaire non trouvé.");
                                        return;
                                    }

                                    // Get current balance
                                    $solde = $fonctionnaire->solde_congé ?? 22;

                                    // Check if there is any balance available
                                    if ($solde <= 0) {
                                        $fail("Vous ne pouvez pas créer un congé car votre solde est épuisé (0 jours).");
                                        return;
                                    }

                                    // Check if requested days exceed available balance
                                    if ($value > $solde) {
                                        $fail("Vous ne pouvez pas demander {$value} jours. Votre solde de congé est de {$solde} jours.");
                                        return;
                                    }
                                }
                            };
                        }
                    ]),

                // Date de retour (calculée automatiquement)
                Forms\Components\DatePicker::make('date_retour')
                    ->label('Date de retour')
                    ->required()
                    ->disabled()
                    ->dehydrated(true),

                // Autorisation de sortie du territoire
                Forms\Components\Checkbox::make('autorisation_sortie_territoire')
                    ->label('Autorisation de sortie du territoire')
                    ->default(false),
            ])
            ->columns(2);
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
                    ->visible(fn ($record) => Gate::allows('manage-conges') && $record->status === 'en cours' && $record->deleted_at === null)
                    ->action(fn ($record) => self::approuverDemande($record)),

                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => Gate::allows('manage-conges') && $record->status === 'en cours' && $record->deleted_at === null)
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
                    ->visible(fn ($record) => Gate::allows('manage-conges') && $record->deleted_at === null)
                    ->url(fn ($record) => route('conge.decision', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('cancel')
                    ->label("Annuler")
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => Gate::allows('manage-conges') && $record->deleted_at === null)
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
                    ->visible(fn ($record) => !Gate::allows('manage-conges') && $record->status === 'en cours' && $record->deleted_at === null)
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
            ])->when(!Gate::allows('manage-conges'), function ($query) {
                // For non-admin users, only show their own records
                $user = Filament::auth()->user();
                return $query->whereHas('fonctionnaire', function ($subQuery) use ($user) {
                    $subQuery->where('id', $user->fonctionnaire_id);
                });
            }));
    }

    // Méthode de routage des pages
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\CongeResource\Pages\ManageCongees::route('/'),
        ];
    }

    // Méthode pour approuver une demande de congé
    public static function approuverDemande(Conge $record)
    {
        // Récupérer le fonctionnaire associé à la demande
        $fonctionnaire = Fonctionnaire::find($record->fonctionnaire_id);

        // Get current balance or initialize to 22 if not set
        $currentBalance = $fonctionnaire->solde_année_act ?? 22;

        // Vérifier si le fonctionnaire a suffisamment de solde
        if ($currentBalance < $record->nombre_jours) {
            throw new \Exception("Solde de congé insuffisant.");
        }

        // Store original balance for notification
        $originalBalance = $currentBalance;

        // Calculate new balance
        $newBalance = $currentBalance - $record->nombre_jours;
            
        // Update the fonctionnaire's balance
        $fonctionnaire->solde_année_act = $newBalance;
        $fonctionnaire->save();

        // Mettre à jour le statut
        $record->update(['status' => 'signée']);

        // Show detailed success notification
        \Filament\Notifications\Notification::make()
            ->title('Congé approuvé avec succès')
            ->body("Le congé de {$record->nombre_jours} jours a été approuvé.\n" .
                  "Ancien solde: {$originalBalance} jours\n" .
                  "Nouveau solde: {$newBalance} jours")
            ->success()
            ->send();

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
        if (!Gate::allows('manage-conges') && $record->status === 'signée') {
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
        $currentDate = clone $startDate;
        $daysToAdd = $numberOfDays;

        // Récupérer tous les jours fériés pour l'année en cours et l'année suivante
        $holidays = \App\Models\JoursFeries::query()
            ->where(function($query) use ($currentDate) {
                $query->whereYear('date_depart', $currentDate->year)
                      ->orWhereYear('date_depart', $currentDate->copy()->addYear()->year);
            })
            ->get()
            ->mapWithKeys(function($holiday) {
                // Convertir la date en format string pour l'utiliser comme clé
                $dateKey = Carbon::parse($holiday->date_depart)->format('Y-m-d');
                return [$dateKey => (int)$holiday->nombre_jours];
            })
            ->toArray();

        // Calculer la date de retour
        while ($daysToAdd > 0) {
            $currentDate->addDay();
            $currentDateStr = $currentDate->format('Y-m-d');

            // Vérifier si c'est un weekend
            if ($currentDate->isWeekend()) {
                continue;
            }

            // Vérifier si c'est un jour férié
            foreach ($holidays as $holidayStart => $holidayDays) {
                $holidayStartDate = Carbon::parse($holidayStart);
                $holidayEndDate = $holidayStartDate->copy()->addDays($holidayDays - 1);

                // Si la date courante est dans la période du jour férié
                if ($currentDate->between($holidayStartDate, $holidayEndDate)) {
                    continue 2; // Continue la boucle principale
                }
            }

            $daysToAdd--;
        }

        return $currentDate;
    }

    public static function boot()
    {
        parent::boot();
    }
}
