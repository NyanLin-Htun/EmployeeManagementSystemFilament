<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('country_id')
                        ->label('Country')
                        ->reactive()
                        ->options(Country::all()->pluck('name','id')->toArray())
                        ->afterStateUpdated(fn (callable $set) => $set('state_id',null))
                        ->required(),
                    Select::make('state_id')
                        ->label('State')
                        ->options(function (callable $get){
                            $country = Country::find($get('country_id'));
                            if(!$country) {
                                return State::all()->pluck('name','id');
                            }
                            return $country->states->pluck('name','id');
                        })
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('city_id',null))
                        ->required(),
                    Select::make('city_id')
                        ->label('City')
                        ->options(function (callable $get){
                            $state = State::find($get('state_id'));
                            if(!$state) {
                                return City::all()->pluck('name','id');
                            }
                            return $state->cities->pluck('name','id');
                        })
                        ->reactive()
                        ->required(),
                    Select::make('department_id')
                        ->relationship('department', 'name')->required(),
                    TextInput::make('first_name')->required()->maxLength(255),
                    TextInput::make('last_name')->required()->maxLength(255),
                    TextInput::make('address')->required()->maxLength(255),
                    TextInput::make('zip_code')->required()->maxLength(5),
                    DatePicker::make('birth_date')->required(),
                    DatePicker::make('date_hired')->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('address'),
                TextColumn::make('country.name')->sortable(),
                TextColumn::make('state.name')->sortable(),
                TextColumn::make('city.name')->sortable(),
                TextColumn::make('department.name')->sortable(),
                TextColumn::make('birth_date')->date(),
                TextColumn::make('date_hired')->date(),
                TextColumn::make('created_at')->dateTime()
            ])
            ->filters([
                // SelectFilter::make('department')->relationship('department', 'name')
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
