<?php

namespace App\Filament\Organization\Resources;

use App\Models\Domain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Organization\Resources\DomainResource\Pages;

class DomainResource extends Resource
{
    use \App\Filament\Organization\Concerns\ScopesToOrganization;

    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('host')->required(),
            Forms\Components\Select::make('type')->options(['subdomain' => 'Subdomain', 'custom' => 'Custom']),
            Forms\Components\Select::make('status')->options(['pending' => 'Pending', 'verified' => 'Verified', 'failed' => 'Failed']),
            Forms\Components\Toggle::make('ssl_enabled')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('host')->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('status')->badge()
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
