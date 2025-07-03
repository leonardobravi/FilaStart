<?php

namespace App\Filament\Resources\CrudResource\RelationManagers;

use App\Enums\CrudTypes;
use App\Enums\CustomTraitTypes;
use App\Models\Crud;
use App\Models\Panel;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomTraitsRelationManager extends RelationManager
{
    protected static string $relationship = 'customTraits';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        /** @var Crud $ownerRecord */
        return $ownerRecord->type === CrudTypes::CRUD;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->live()
                    ->options(CustomTraitTypes::class)
                    ->default(CustomTraitTypes::ANY->value)
                    ->required(),
                Forms\Components\TextInput::make('namespace')
                    ->regex('/^\w+(?:\\\\\w+)*$/i')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('alias')
                    ->alpha()
                    ->maxLength(50)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visual_title')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('visual_title'),
                Tables\Columns\TextColumn::make('namespace'),
                Tables\Columns\TextColumn::make('alias'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data) {
                        /** @var Panel $tenant */
                        $tenant = Filament::getTenant();

                        $data['panel_id'] = $tenant->id;
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
