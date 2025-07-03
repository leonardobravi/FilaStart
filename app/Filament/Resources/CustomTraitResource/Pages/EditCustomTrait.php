<?php

namespace App\Filament\Resources\CustomTraitResource\Pages;

use App\Filament\Resources\CustomTraitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomTrait extends EditRecord
{
    protected static string $resource = CustomTraitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
