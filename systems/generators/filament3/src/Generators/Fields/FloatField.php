<?php

namespace Generators\Filament3\Generators\Fields;

use Generators\Laravel11\Generators\MigrationLineGenerator;

class FloatField extends BaseField
{
    protected string $formComponentClass = 'TextInput';

    protected string $tableColumnClass = 'TextColumn';

    protected function resolveFormComponent(): void
    {
        $this->formKey = $this->field->key;
    }

    protected function resolveTableColumn(): void
    {
        $this->tableKey = $this->field->key;
    }

    protected function resolveFormOptions(): string
    {
        $options = PHP_EOL;
        // TODO: This needs format to be added
        $options .= '    ->numeric()';

        return $options.parent::resolveFormOptions();
    }

    protected function resolveTableOptions(): string
    {
        $options = PHP_EOL;
        // TODO: This needs format to be added
        $options .= '    ->numeric()';

        return $options.parent::resolveTableOptions();
    }

    public function getMigrationLine(bool $change = false): string
    {
        return (new MigrationLineGenerator($change))
            ->setType('double')
            ->setKey($this->field->key)
            ->toString();
    }
}
