<?php

namespace App\Filament\Resources\PanelDeploymentResource\Pages;

use App\Enums\HeroIcons;
use App\Filament\Resources\PanelDeploymentResource;
use App\Jobs\Generator\GeneratePanelCodeJob;
use App\Models\CrudField;
use App\Models\Panel;
use App\Models\PanelDeployment;
use App\Services\PanelService;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Bus;
use Ramsey\Uuid\Uuid;

class ListPanelDeployment extends ListRecords
{
    protected static string $resource = PanelDeploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Deploy Now')
                ->icon(HeroIcons::O_ROCKET_LAUNCH->value)
                ->action(function () {
                    /** @var Panel $panel */
                    $panel = Filament::getTenant();

                    /** @var PanelDeployment $newDeployment */
                    $newDeployment = $panel->panelDeployments()->create([
                        'status' => 'pending',
                        'deployment_id' => Uuid::uuid4(),
                    ]);

                    $newDeployment->addNewMessage('Generation started at ' . now()->toDateTimeString() . PHP_EOL);

                    Bus::batch([
                        new GeneratePanelCodeJob($panel->id, $newDeployment->id),
                    ])
                        ->name($newDeployment->deployment_id)
                        ->catch(function () use ($newDeployment) {
                            $newDeployment?->addNewMessage('Generation has failed...' . PHP_EOL);

                            $newDeployment?->update([
                                'status' => 'failed',
                            ]);
                        })
                        ->then(function () use ($panel, $newDeployment) {
                            $service = new PanelService($panel);
                            $filePath = $service->zipFiles($newDeployment);

                            $newDeployment?->update([
                                'status' => 'success',
                                'file_path' => $filePath,
                            ]);

                            $newDeployment?->addNewMessage('Generation completed at ' . now()->toDateTimeString() . PHP_EOL);
                        })
                        ->dispatch();

                    $newDeployment->fresh();

                    // $this->dispatch('$refresh');

                    Notification::make()
                        ->title("Deployment completed successful")
                        ->success()
                        ->persistent()
                        ->send();
                })
                ->disabled(function () {
                    return !empty(PanelDeployment::where('status', 'pending')->first()) ||
                        empty(CrudField::where('updated_at', '>=', PanelDeployment::max('created_at'))->first());
                })
        ];
    }
}
