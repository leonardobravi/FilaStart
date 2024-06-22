<?php

namespace Generators\Filament3\Generators\Fields;

use Generators\Laravel11\Generators\MigrationLineGenerator;

class CheckboxField extends BaseField
{
    protected string $formComponentClass = 'Checkbox';

    protected string $tableColumnClass = 'CheckboxColumn';

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
            ->setType('boolean')
            ->setKey($this->field->key)
            ->setDefault(false)
            ->toString();
    }
}
