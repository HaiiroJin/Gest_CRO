<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttestationTravailResource\Pages;
use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class AttestationTravailResource extends Resource
{
    protected static ?string $model = AttestationTravail::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Attestations de Travail';
    protected static ?string $pluralLabel = 'Attestations';
    protected static ?string $navigationGroup = 'Gestion des Demandes';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\Select::make('fonctionnaire_id')
                ->label('Fonctionnaire')
                ->options(function () {
                    if (auth()->user()->hasRole('super_admin')) {
                        return Fonctionnaire::all()
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

            \Filament\Forms\Components\DatePicker::make('date_demande')
                ->label('Date de la demande')
                ->default(now())
                ->disabled()
                ->format('Y-m-d')
                ->dehydrated(true)
                ->required(),
            \Filament\Forms\Components\Radio::make('langue')
                ->label('Langue')
                ->options([
                    'fr' => 'Français',
                    'ar' => 'Arabe',
                ])
                ->default('fr')
                ->required()
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->color(fn($state)=> match($state){
                        'en cours' => 'primary',
                        'signé' => 'success',
                        'rejeté' => 'danger',
                    }),
                TextColumn::make('langue')
                    ->label('Langue')
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'fr' => 'Français',
                        'ar' => 'Arabe',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filtrer par statut')
                    ->options([
                        'en cours' => 'En cours',
                        'signé' => 'Signé',
                        'rejeté' => 'Rejeté',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Signer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => 
                        auth()->user()->hasRole('super_admin') && 
                        $record->status === 'en cours' && 
                        $record->deleted_at === null
                    )
                    ->action(fn ($record) => self::approveDemande($record)),

                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')
                            ->label('Motif du rejet')
                            ->required()
                    ])
                    ->visible(fn ($record) => 
                        auth()->user()->hasRole('super_admin') && 
                        $record->status === 'en cours' && 
                        $record->deleted_at === null
                    )
                    ->action(fn ($record, $data) => self::rejectDemande($record, $data['rejection_reason'])),

                Action::make('print')
                    ->label('Attestation')
                    ->icon('heroicon-o-document')
                    ->color('primary')
                    ->visible(fn ($record) => ($record->status === 'en cours' || $record->status === 'signé') && auth()->user()->hasRole('super_admin'))
                    ->url(fn ($record) => route('attestation.print', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                Action::make('download_demande')
                    ->label('Demande')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn ($record) => $record->fonctionnaire_id === auth()->user()->fonctionnaire_id)
                    ->url(fn ($record) => route('attestation.demande', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                DeleteAction::make(),
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
            ->defaultSort('date_demande', 'desc')
            ->persistFiltersInSession()
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function approveDemande(Model $record)
    {
        $record->update([
            'status' => 'signé',
        ]);
    }

    public static function rejectDemande(Model $record, string $reason)
    {
        $record->update([
            'status' => 'rejeté',
            'raison_rejection' => $reason,
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return AttestationTravail::query()
            ->withTrashed()
            ->when(auth()->check() && !auth()->user()->hasRole('super_admin'), function ($query) {
                return $query->where('fonctionnaire_id', auth()->user()->fonctionnaire_id);
            });
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('super_admin') ? 'Gestion des Demandes' : 'Demandes';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttestationTravails::route('/'),
        ];
    }
}
