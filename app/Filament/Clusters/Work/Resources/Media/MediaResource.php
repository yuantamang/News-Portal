<?php

namespace App\Filament\Clusters\Work\Resources\Media;

use App\Filament\Clusters\Work\Resources\Media\Pages\EditMedia;
use App\Filament\Clusters\Work\Resources\Media\Pages\ListMedia;
use App\Filament\Clusters\Work\Resources\Media\Pages\ViewMedia;
use App\Filament\Clusters\Work\Resources\Media\Schemas\MediaForm;
use App\Filament\Clusters\Work\Resources\Media\Schemas\MediaInfolist;
use App\Filament\Clusters\Work\Resources\Media\Tables\MediaTable;
use App\Filament\Clusters\Work\WorkCluster;
use App\Models\Media;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    protected static ?string $cluster = WorkCluster::class;

    protected static ?string $navigationLabel = 'Media';

    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
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
            'index' => ListMedia::route('/'),
            'view' => ViewMedia::route('/{record}'),
            'edit' => EditMedia::route('/{record}/edit'),
        ];
    }
}
