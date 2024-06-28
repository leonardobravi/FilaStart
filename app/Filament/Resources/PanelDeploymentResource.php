<?php

namespace App\Filament\Resources;

use App\Enums\HeroIcons;
use App\Jobs\Generator\GeneratePanelCodeJob;
use App\Models\CrudField;
use App\Models\Panel;
use App\Models\PanelDeployment;
use App\Services\PanelService;
use Filament\Actions\StaticAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\HtmlString;
use Ramsey\Uuid\Uuid;

class PanelDeploymentResource extends Resource
{
    protected static ?string $model = PanelDeployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected static ?string $modelLabel = 'Deployment';

    protected static ?string $pluralModelLabel = 'Deployments';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\ViewColumn::make('status')
                    ->view('filament.tables.columns.deployment-status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deployment_id')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('files')
                    ->formatStateUsing(fn(PanelDeployment $record) => $record->panel()->panelFiles()->count()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon(HeroIcons::O_ARROW_DOWN_TRAY->value)
                    ->url(fn(PanelDeployment $record): ?string => $record->file_path)
                    ->visible(fn(PanelDeployment $record): bool => !empty($record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('details')
                    ->icon(HeroIcons::O_EYE->value)
                    ->modalContent(fn(PanelDeployment $record): View => view('filament.modals.panel-deployment-details', ['panelDeployment' => $record]))
                    ->modalHeading(fn(PanelDeployment $record) => new HtmlString(match ($record->status) {
                        'pending' => '<div class="text-warning">Deployment is pending</div>',
                        'failed' => '<div class="text-danger">Deployment failed</div>',
                        'success' => '<div class="text-success">Deployment successful</div>',
                    }))
//                    ->modalHeading(fn(PanelDeployment $record) => new HtmlString(match ($record->status) {
//                        'pending' => '<div class="text-warning">' . svg(HeroIcons::O_CLOCK->value, ['class' => 'h-6 w-6 d-inline'])->toHtml() . ' Deployment is pending</div>',
//                        'failed' => '<div class="text-danger">' . svg(HeroIcons::O_EXCLAMATION_CIRCLE->value, ['class' => 'h-6 w-6d-inline'])->toHtml() . ' Deployment failed</div>',
//                        'success' => '<div class="success">' . svg(HeroIcons::O_CHECK_CIRCLE->value, ['class' => 'h-6 w-6 d-inline'])->toHtml() . ' Deployment successful</div>',
//                    }))
//                    ->modalIcon(fn(PanelDeployment $record): string => match ($record->status) {
//                        'pending' => HeroIcons::O_CLOCK->value,
//                        'failed' => HeroIcons::O_EXCLAMATION_CIRCLE->value,
//                        'success' => HeroIcons::O_CHECK_CIRCLE->value,
//                    })
                    ->modalCancelAction(false)
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
//            ->headerActions([
//                Tables\Actions\Action::make('Deploy Now')
//                    ->icon(HeroIcons::O_ROCKET_LAUNCH->value)
//                    ->action(function () {
//                        /** @var Panel $panel */
//                        $panel = Filament::getTenant();
//
//                        $newDeployment = $panel->panelDeployments()->create([
//                            'status' => 'pending',
//                            'deployment_id' => Uuid::uuid4(),
//                        ]);
//
//                        $newDeployment->addNewMessage('Generation started at ' . now()->toDateTimeString() . PHP_EOL);
//
//                        Bus::batch([
//                            new GeneratePanelCodeJob($panel->id, $newDeployment->id),
//                        ])
//                            ->name($newDeployment->deployment_id)
//                            ->catch(function () use ($newDeployment) {
//                                $newDeployment?->addNewMessage('Generation has failed...' . PHP_EOL);
//
//                                $newDeployment?->update([
//                                    'status' => 'failed',
//                                ]);
//                            })
//                            ->then(function () use ($panel, $newDeployment) {
//                                $service = new PanelService($panel);
//                                $filePath = $service->zipFiles();
//
//                                $newDeployment?->update([
//                                    'status' => 'success',
//                                    'file_path' => $filePath,
//                                ]);
//
//                                $newDeployment?->addNewMessage('Generation completed at ' . now()->toDateTimeString() . PHP_EOL);
//                            })
//                            ->dispatch();
//
//                        $newDeployment->fresh();
//
//                        // $this->dispatch('$refresh');
//
//                        Notification::make()
//                            ->title("Deployment completed successful")
//                            ->success()
//                            ->persistent()
//                            ->send();
//                    })
//                    ->disabled(function () {
//                        return !empty(PanelDeployment::where('status', 'pending')->first()) ||
//                            empty(CrudField::where('updated_at', '>=', PanelDeployment::max('created_at'))->first());
//                })
//            ])
            ->recordUrl(null);
    }

//    public static function getRelations(): array
//    {
//        return [
//            FieldsRelationManager::class,
//        ];
//    }

    public static function getPages(): array
    {
        return [
            'index' => PanelDeploymentResource\Pages\ListPanelDeployment::route('/'),
        ];
    }
}
