<?php

namespace App\Filament\Resources\PanelDeploymentResource\Pages;

use App\Filament\Resources\PanelDeploymentResource;
use Filament\Resources\Pages\ListRecords;

class ListPanelDeployment extends ListRecords
{
    protected static string $resource = PanelDeploymentResource::class;

//    protected function getHeaderActions(): array
//    {
//        return [
//            Actions\CreateAction::make(),
//        ];
//    }
}
