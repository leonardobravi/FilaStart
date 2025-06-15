<?php

namespace Generators\Filament3\Generators\Fields;

use Generators\Laravel11\Generators\MigrationLineGenerator;

class MarkdownField extends BaseField
{
    protected string $formComponentClass = 'MarkdownEditor';

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
            ->setType('text')
            ->setKey($this->field->key)
            ->setChange($change)
            ->setNullable($this->field->nullable)
            ->toString();
    }
}
