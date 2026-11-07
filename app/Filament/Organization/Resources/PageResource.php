<?php

namespace App\Filament\Organization\Resources;

use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Organization\Resources\PageResource\Pages;

class PageResource extends Resource
{
    use \App\Filament\Organization\Concerns\ScopesToOrganization;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Builder';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('slug'),
            Forms\Components\Select::make('project_id')->relationship('project', 'name')->required(),
            Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(),
            Forms\Components\TextInput::make('subdomain'),
            Forms\Components\TextInput::make('publish_path'),
            Forms\Components\Textarea::make('sections')->rows(12)->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('subdomain'),
                Tables\Columns\TextColumn::make('published_at')->dateTime()
            ])
            ->actions([
                Tables\Actions\Action::make('editor')
                    ->label('Editor')
                    ->url(fn ($record) => route('editor.pages', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(\App\Services\Pages\PagePublisher::class)->publish($record, null, null, auth()->id());
                    }),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
