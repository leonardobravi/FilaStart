<?php

namespace App\Filament\Resources\CustomTraitResource\Pages;

use App\Filament\Resources\CustomTraitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomTraits extends ListRecords
{
    protected static string $resource = CustomTraitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
