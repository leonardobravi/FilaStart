<?php

namespace Generators\Filament3\Generators\Fields;

use Generators\Laravel11\Generators\MigrationLineGenerator;

class JsonField extends BaseField
{
    protected string $formComponentClass = 'KeyValue';

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
        return (new MigrationLineGenerator())
            ->setType('json')
            ->setKey($this->field->key)
            ->setChange($change)
            ->setNullable($this->field->nullable)
            ->toString();
    }
}
