<?php

namespace App\Filament\Clusters\Work\Resources\Advertisements;

use App\Filament\Clusters\Work\Resources\Advertisements\Pages\CreateAdvertisement;
use App\Filament\Clusters\Work\Resources\Advertisements\Pages\EditAdvertisement;
use App\Filament\Clusters\Work\Resources\Advertisements\Pages\ListAdvertisements;
use App\Filament\Clusters\Work\Resources\Advertisements\Pages\ViewAdvertisement;
use App\Filament\Clusters\Work\Resources\Advertisements\Schemas\AdvertisementForm;
use App\Filament\Clusters\Work\Resources\Advertisements\Schemas\AdvertisementInfolist;
use App\Filament\Clusters\Work\Resources\Advertisements\Tables\AdvertisementsTable;
use App\Filament\Clusters\Work\WorkCluster;
use App\Models\Advertisement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Megaphone;

    protected static ?string $cluster = WorkCluster::class;

    public static function form(Schema $schema): Schema
    {
        return AdvertisementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdvertisementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdvertisementsTable::configure($table);
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
            'index' => ListAdvertisements::route('/'),
            'create' => CreateAdvertisement::route('/create'),
            'view' => ViewAdvertisement::route('/{record}'),
            'edit' => EditAdvertisement::route('/{record}/edit'),
        ];
    }
}
