<?php

namespace App\Filament\Organization\Resources;

use App\Models\Funnel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Organization\Resources\FunnelResource\Pages;

class FunnelResource extends Resource
{
    use \App\Filament\Organization\Concerns\ScopesToOrganization;

    protected static ?string $model = Funnel::class;

    protected static ?string $navigationIcon = 'heroicon-o-funnel';

    protected static ?string $navigationGroup = 'Builder';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('project_id')->relationship('project', 'name')->required(),
            Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required(),
            Forms\Components\Textarea::make('description')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('project.name')
            ])
            ->actions([
                Tables\Actions\Action::make('editor')
                    ->label('Editor')
                    ->url(fn ($record) => route('editor.funnels', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListFunnels::route('/'),
            'create' => Pages\CreateFunnel::route('/create'),
            'edit' => Pages\EditFunnel::route('/{record}/edit'),
        ];
    }
}
