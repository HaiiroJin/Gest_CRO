<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Fonctionnaire;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use App\Models\Profile;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Utilisateurs';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        $fonctionnaire = new Fonctionnaire();
        return $form
            ->schema([
                Forms\Components\Select::make('fonctionnaire_id')
                    ->options($fonctionnaire->getFonctionnaireOptions())
                    ->searchable()
                    ->required()
                    ->label('Fonctionnaire')
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if ($state) {
                            $selectedFonctionnaire = Fonctionnaire::find($state);
                            
                            $nom = Str::slug($selectedFonctionnaire->nom);
                            $prenom = Str::slug($selectedFonctionnaire->prenom);
                            $email = "{$nom}.{$prenom}@cr-oriental.ma";
                            $set('email', $email);
                        }
                    }),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->hidden()
                    ->default(function ($get) {
                        $fonctionnaireId = $get('fonctionnaire_id');
                        if ($fonctionnaireId) {
                            $selectedFonctionnaire = Fonctionnaire::find($fonctionnaireId);
                            return "{$selectedFonctionnaire->nom} {$selectedFonctionnaire->prenom}";
                        }
                        return null;
                    }),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fonctionnaire.nom')
                    ->label('Nom'),
                Tables\Columns\TextColumn::make('fonctionnaire.prenom')
                    ->label('Prénom'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rôles')
                    ->badge()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

}
