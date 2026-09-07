<?php

namespace App\Filament\Clusters\Work\Resources\Tags;

use App\Filament\Clusters\Work\Resources\Tags\Pages\CreateTag;
use App\Filament\Clusters\Work\Resources\Tags\Pages\EditTag;
use App\Filament\Clusters\Work\Resources\Tags\Pages\ListTags;
use App\Filament\Clusters\Work\Resources\Tags\Pages\ViewTag;
use App\Filament\Clusters\Work\Resources\Tags\Schemas\TagForm;
use App\Filament\Clusters\Work\Resources\Tags\Schemas\TagInfolist;
use App\Filament\Clusters\Work\Resources\Tags\Tables\TagsTable;
use App\Filament\Clusters\Work\WorkCluster;
use App\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $cluster = WorkCluster::class;

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TagInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'view' => ViewTag::route('/{record}'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
