<?php

namespace App\Filament\Organization\Resources;

use App\Models\Experiment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Organization\Resources\ExperimentResource\Pages;

class ExperimentResource extends Resource
{
    use \App\Filament\Organization\Concerns\ScopesToOrganization;

    protected static ?string $model = Experiment::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Optimization';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('project_id')->relationship('project', 'name')->required(),
            Forms\Components\Select::make('page_id')->relationship('page', 'title'),
            Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'running' => 'Running', 'paused' => 'Paused', 'completed' => 'Completed'])->required(),
            Forms\Components\TextInput::make('goal_event')->default('conversion'),
            Forms\Components\Textarea::make('hypothesis')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('goal_event')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExperiments::route('/'),
            'create' => Pages\CreateExperiment::route('/create'),
            'edit' => Pages\EditExperiment::route('/{record}/edit'),
        ];
    }
}
