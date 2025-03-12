<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FonctionnaireResource\Pages;
use App\Models\Fonctionnaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class FonctionnaireResource extends Resource
{
    protected static ?string $model = Fonctionnaire::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestion Ressources Humaines';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nom_ar')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('prenom_ar')
                            ->maxLength(255),
                        Forms\Components\Select::make('civilite')
                            ->options([
                                'M' => 'M',
                                'Mme' => 'Mme',
                            ]),
                        Forms\Components\DatePicker::make('date_naissance')
                            ->required(),
                        Forms\Components\TextInput::make('cin')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tel')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rib')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Textarea::make('adresse')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan('full'),
                    ])->columns(4),

                Forms\Components\Section::make('Informations professionnelles')
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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('situation')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('matricule_aujour')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('solde_année_prec')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('solde_année_act')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('solde_congé')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_recruitement')
                            ->required(),
                        Forms\Components\DatePicker::make('date_affectation_cro')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('civilite')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('prenom')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_ar')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('prenom_ar')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rib')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tel')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('adresse')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('matricule_aujour')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('solde_année_prec')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('solde_année_act')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('solde_congé')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('situation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('corps.libelle')
                    ->label('Corps')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('grade.libelle')
                    ->label('Grade')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('groupe.libelle')
                    ->label('Groupe')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('direction.libelle')
                    ->label('Direction')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('division.libelle')
                    ->label('Division')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('service.libelle')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('corps_id')
                    ->options(\App\Models\Corps::getOptions())
                    ->label('Corps')
                    ->searchable()
                    ->columnSpan('full'),
                Tables\Filters\SelectFilter::make('grade_id')
                    ->options(\App\Models\Grade::getOptions())
                    ->label('Grade')
                    ->searchable()
                    ->columnSpan('full'),
                Tables\Filters\SelectFilter::make('groupe_id')
                    ->options(\App\Models\Groupe::getOptions())
                    ->label('Groupe')
                    ->searchable()
                    ->columnSpan('full'),
                Tables\Filters\SelectFilter::make('direction_id')
                    ->options(\App\Models\Direction::getDirectionsOptions())
                    ->label('Direction')
                    ->searchable()
                    ->columnSpan('full'),
                Tables\Filters\SelectFilter::make('division_id')
                    ->options(\App\Models\Division::getDivisionsOptions())
                    ->label('Division')
                    ->searchable()
                    ->columnSpan('full'),
                Tables\Filters\SelectFilter::make('service_id')
                    ->options(\App\Models\Service::getServicesOptions())
                    ->label('Service')
                    ->searchable()
                    ->columnSpan('full'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informations personnelles')
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
                            ->label('Adresse'),
                    ])->columns(4),
                Section::make('Informations professionnelles')
                    ->schema([
                        TextEntry::make('corps.libelle')
                            ->label('Corps'),
                        TextEntry::make('grade.libelle')
                            ->label('Grade'),
                        TextEntry::make('groupe.libelle')
                            ->label('Groupe'),
                        TextEntry::make('direction.libelle')
                            ->label('Direction'),
                        TextEntry::make('division.libelle')
                            ->label('Division'),
                        TextEntry::make('service.libelle')
                            ->label('Service'),
                        TextEntry::make('matricule_aujour')
                            ->label('Matricule aujour'),
                        TextEntry::make('date_recruitement')
                            ->label('Date recruitement'),
                        TextEntry::make('date_affectation_cro')
                            ->label('Date affectation cro'),
                        TextEntry::make('solde_année_prec')
                            ->label('Solde année préc'),
                        TextEntry::make('solde_année_act')
                            ->label('Solde année act'),
                        TextEntry::make('solde_congé')
                            ->label('Solde congé'),
                    ])->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFonctionnaires::route('/'),
        ];
    }
}
