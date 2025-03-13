<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttestationTravailResource\Pages;
use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;
use App\Notifications\AttestationApproved;
use App\Notifications\AttestationRejected;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AttestationTravailResource extends Resource
{
    protected static ?string $model = AttestationTravail::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Attestations de Travail';
    protected static ?string $pluralLabel = 'Attestations';
    protected static ?string $navigationGroup = 'Gestion Ressources Humaines';

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\TextInput::make('motif')
                ->label('Motif')
                ->placeholder('Ex: Dossier bancaire, Visa...')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')->label('Nom')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('prenom')->label('Prénom')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match ($record->status) {
                        'en cours' => 'gray',
                        'au parapheur' => 'blue',
                        'signé' => 'green',
                        'rejeté' => 'red',
                    }),
                Tables\Columns\TextColumn::make('parapheur')
                    ->label('Parapheur')
                    ->badge()
                    ->color(fn ($record) => $record->parapheur === 'signé' ? 'green' : 'yellow'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrer par statut')
                    ->options([
                        'en cours' => 'En cours',
                        'au parapheur' => 'Au parapheur',
                        'signé' => 'Signé',
                        'rejeté' => 'Rejeté',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Mettre au parapheur')
                    ->icon('heroicon-o-document-check')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->status === 'en cours')
                    ->action(fn ($record) => self::sendToParapheur($record)),

                Action::make('finalize')
                    ->label('Signer l’attestation')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->status === 'au parapheur')
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
                    ->visible(fn ($record) => auth()->user()->hasRole('super_admin') && $record->status === 'en cours')
                    ->action(fn ($record, $data) => self::rejectDemande($record, $data['rejection_reason'])),

                Action::make('print')
                    ->label('Imprimer')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'signé' && $record->demande)
                    ->url(fn ($record) => route('attestation.print', ['id' => $record->id]))
                    ->openUrlInNewTab(),

                DeleteAction::make(),
            ])
            ->defaultSort('date_demande', 'desc')
            ->persistFiltersInSession();
    }

    public static function sendToParapheur(Model $record)
    {
        $record->update([
            'status' => 'au parapheur',
        ]);
    }

    public static function approveDemande(Model $record)
{
    $fonctionnaire = Fonctionnaire::with(['corps', 'grade'])->find($record->fonctionaire_id);

    if (!$fonctionnaire) {
        throw new \Exception("Fonctionnaire not found for ID: " . $record->fonctionaire_id);
    }

    // Generate the attestation HTML
    $htmlContent = view('attestation-travail', ['fonctionnaire' => $fonctionnaire])->render();

    // Update the record
    $record->update([
        'status' => 'signé',
        'parapheur' => 'signé',
        'demande' => $htmlContent,
    ]);

    // Save to storage
    Storage::disk('public')->put("attestations/attestation_{$fonctionnaire->id}.html", $htmlContent);

    // Send Filament Notification
    Notification::make()
        ->title('Attestation signée')
        ->body('Votre attestation de travail a été signée et est disponible pour téléchargement.')
        ->success()
        ->sendToDatabase($fonctionnaire->user);
}

public static function rejectDemande(Model $record, string $reason)
{
    $record->update([
        'status' => 'rejeté',
        'rejection_reason' => $reason,
    ]);

    // Send Filament Notification
    $fonctionnaire = Fonctionnaire::find($record->fonctionaire_id);
    if ($fonctionnaire) {
        Notification::make()
            ->title('Attestation rejetée')
            ->body("Votre demande a été rejetée. Motif : $reason")
            ->danger()
            ->sendToDatabase($fonctionnaire->user);
    }
}

    public static function getEloquentQuery(): Builder
    {
        return AttestationTravail::query()->when(auth()->check() && !auth()->user()->hasRole('super_admin'), function ($query) {
            return $query->where('fonctionaire_id', auth()->user()->fonctionnaire_id);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttestationTravails::route('/'),
        ];
    }
}
