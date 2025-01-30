<?php

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')->label('first_name'), // Changed from 'name' to 'first_name'
                TextInput::make('last_name')->label('last_name'),
                TextInput::make('email')->label('email'),
                TextInput::make('phone_number')->label('phone_number'),
                TextInput::make('role')->label('role'),
                TextInput::make('password')->label('password'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Textcolumn::make('first_name')->searchable()->sortable(), // Changed from 'name' to 'first_name'
                Textcolumn::make('last_name'),
                Textcolumn::make('email'),
                Textcolumn::make('phone_number')->formatStateUsing(function ($state) {
                    return preg_replace('/(\d{3})(\d{3})(\d{3})/', '$1-$2-$3', $state);
                }),
                Textcolumn::make('role')->formatStateUsing(fn($state) => $state->getLabel()) //label z role enum
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->form([
                    Fieldset::make('Podstawowe informacje')
                        ->schema([
                            TextInput::make('first_name')->label('first_name'), // Changed from 'name' to 'first_name'
                            TextInput::make('last_name')->label('last_name'),
                            TextInput::make('phone_number')->label('phone_number'),
                            TextInput::make('email')->label('email'),
                            Select::make('role')
                                ->options(
                                    collect(Role::cases())
                                        ->mapWithKeys(fn($role) => [$role->value => $role->getLabel()])
                                        ->toArray()
                                ),
                            TextInput::make('password')->label('password'),
                        ])->columns(3),
                ]),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
