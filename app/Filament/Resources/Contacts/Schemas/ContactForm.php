<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->columnSpanFull()
                    ->schema([
                        Section::make("Contact Detail's")
                            ->description("Write the contact info about you.")
                            ->icon(Heroicon::Phone)
                            ->schema([
                                TextInput::make('phone_number')
                                    ->tel()
                                    ->placeholder("Your Phone Number.")
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required(),
                                TextInput::make('link')
                                    ->required()
                                    ->url()
                                    ->placeholder("Any Link of your page.")
                                    ->columnSpanFull(),
                            ])->collapsible()->columns()
                    ])
            ]);
    }
}
