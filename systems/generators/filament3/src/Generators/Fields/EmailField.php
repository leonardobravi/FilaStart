<?php

namespace Generators\Filament3\Generators\Fields;

use Generators\Laravel11\Generators\MigrationLineGenerator;

class EmailField extends BaseField
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

    public function getMigrationLine(bool $change = false): string
    {
        return (new MigrationLineGenerator($change))
            ->setType('string')
            ->setKey($this->field->key)
            ->toString();
    }
}
