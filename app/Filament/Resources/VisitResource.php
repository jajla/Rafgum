<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VisitResource\Pages;
use App\Filament\Resources\VisitResource\RelationManagers;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VisitResource extends Resource
{
    protected static ?string $model = Visit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function getPluralLabel(): string
    {
        return __('trans.resources.visits'); // "Użytkownik"
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label(__('trans.form.user_id'))
                    ->native(false)
                    ->options(User::query()
                        ->get()
                        ->mapWithKeys(fn($user) => [$user->id => $user->last_name]))
                    ->required(),
                DatePicker::make('date')
                    ->label(__('trans.form.date'))
                    ->minDate(now()->subDays(1))
                    ->displayFormat('d F Y')
                    ->required(),
                TimePicker::make('time')
                    ->label(__('trans.form.time'))
                    ->native(false)
                    ->minutesStep(30)
                    ->seconds(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(['20','30','40','all'])
            ->defaultPaginationPageOption(30)
            //tutaj tez nie da sie tlumaczyc
            ->columns([
                Split::make([
                    TextColumn::make('user.last_name')
                        ->searchable()
                        ->alignCenter(),
                    TextColumn::make('time')
                        ->alignCenter()
                        ->formatStateUsing(fn($state) => date('H:i', strtotime($state))),
                    TextColumn::make('date')
                        ->minDate(now()->subDays(1))
                        ->alignCenter()
                        ->formatStateUsing(fn($state) => Carbon::parse($state)
                            ->translatedFormat('j F l')),
                ])->from('sm')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Section::make()
                            ->schema([
                                Grid::make([
                                    'sm' => 1,
                                    'md' => 2,
                                    'lg' => 3,
                                    'xl' => 3,
                                    '2xl' => 2,
                                ])
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('trans.form.user_id'))
                                            ->native(false)
                                            ->options(User::query()
                                                ->get()
                                                ->mapWithKeys(fn($user) => [$user->id => $user->last_name]))
                                            ->required(),
                                        DatePicker::make('date')
                                            ->label(__('trans.form.date'))
                                            ->minDate(now()->subDays(1))
                                            ->displayFormat('d F Y')
                                            ->required(),
                                        TimePicker::make('time')
                                            ->label(__('trans.form.time'))
                                            ->native(false)
                                            ->minutesStep(30)
                                            ->seconds(false)
                                            ->required(),
                                    ]),

                            ]),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ]);
        /*->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);*/
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVisits::route('/'),
        ];
    }
}
