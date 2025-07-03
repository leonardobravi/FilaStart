<?php

namespace App\Filament\Resources\CustomTraitResource\Pages;

use App\Filament\Resources\CustomTraitResource;
use App\Models\Panel;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomTrait extends CreateRecord
{
    protected static string $resource = CustomTraitResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var Panel $tenant */
        $tenant = Filament::getTenant();

        return [
            ...$data,
            'user_id' => auth()->id(),
            'panel_id' => $tenant->id,
        ];
    }
}
