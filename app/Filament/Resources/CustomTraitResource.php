<?php

namespace App\Filament\Resources;

use App\Enums\CustomTraitTypes;
use App\Filament\Resources\CustomTraitResource\Pages;
use App\Models\CustomTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomTraitResource extends Resource
{
    protected static ?string $model = CustomTrait::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $modelLabel = 'Custom Trait';

    protected static ?string $pluralModelLabel = 'Custom Traits';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('visual_title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('namespace')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alias')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomTraits::route('/'),
            'create' => Pages\CreateCustomTrait::route('/create'),
            'edit' => Pages\EditCustomTrait::route('/{record}/edit'),
        ];
    }
}
