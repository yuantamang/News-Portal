<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make('Contact Detail')
                            ->description("Here the information are displayed.")
                            ->icon(Heroicon::Phone)
                            ->schema([
                                TextEntry::make('phone_number'),
                                TextEntry::make('email')
                                    ->label('Email address'),
                                TextEntry::make('link')
                                    ->columnSpanFull(),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ])->collapsible()->columns()
                    ])
            ]);
    }
}
