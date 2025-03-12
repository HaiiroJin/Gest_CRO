<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttestationTravailResource\Pages;
use App\Models\AttestationTravail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Facades\Filament;

class AttestationTravailResource extends Resource
{
    protected static ?string $model = AttestationTravail::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Attestations de Travail';

    protected static ?string $pluralLabel = 'Attestations';

    protected static ?string $navigationGroup = 'Gestion Ressources Humaines';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
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
                        'signé' => 'green',
                        'rejeté' => 'red',
                    }),
                Tables\Columns\TextColumn::make('date_demande')->label('Date de Demande')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrer par statut')
                    ->options([
                        'en cours' => 'En cours',
                        'signé' => 'Signé',
                        'rejeté' => 'Rejeté',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => Filament::auth()->user()->hasRole('super_admin') && $record->status === 'en cours')
                    ->action(fn ($record) => self::approveDemande($record)),
                Action::make('reject')
                    ->label('Rejeter')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => Filament::auth()->user()->hasRole('super_admin') && $record->status === 'en cours')
                    ->action(fn ($record) => self::rejectDemande($record)),
                Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-download')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'signé' && $record->demande)
                    ->url(fn ($record) => route('attestation.download', $record->id), true),
                DeleteAction::make()->visible(fn ($record) => Filament::auth()->user()->hasRole('super_admin')),
            ])
            ->defaultSort('date_demande', 'desc')
            ->persistFiltersInSession();
    }

    public static function approveDemande(Model $record)
    {
        $htmlContent = view('html.attestation', ['demande' => $record])->render();

        $record->update([
            'status' => 'signé',
            'demande' => $htmlContent,
        ]);

        Storage::disk('public')->put("attestations/attestation_{$record->fonctionaire_id}.html", $htmlContent);
    }

    public static function rejectDemande(Model $record)
    {
        $record->update(['status' => 'rejeté']);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        return $user->hasRole('super_admin')
            ? AttestationTravail::query()
            : AttestationTravail::where('fonctionaire_id', $user->fonctionnaire_id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttestationTravails::route('/'),
        ];
    }
}
