<?php

namespace Generators\Laravel11\Generators;

use App\Enums\CrudFieldTypes;
use App\Models\Crud;
use App\Models\CrudField;
use App\Models\PanelDeployment;
use Exception;
use Generators\Filament3\Generators\Fields\RetrieveGeneratorForField;

class MigrationGenerator
{

    protected $isNewTable = true;

    public function __construct(public Crud $crud, public PanelDeployment $deployment)
    {
        if (!$this->crud->relationLoaded('fields')) {
            $this->crud->load(['fields', 'fields.crudFieldOptions']);
        }
        $this->isNewTable = empty($this->deployment->prev()) || $this->crud->created_at > $this->deployment->prev()->created_at;
    }

    public function generate(): string
    {
        $templateName = $this->isNewTable ? 'migrationCreate' : 'migrationUpdate';
        $output = view('laravel11::' . $templateName, [
            'isNewTable' => $this->isNewTable,
            'uses' => $this->generateUses(),
            'tableName' => $this->generateTableName(),
            'tableColumns' => $this->generateColumns(),
        ])->render();

        return '<?php' . PHP_EOL . PHP_EOL . $output;
    }

    public function generateManyToMany(Crud $crud, CrudField $field): string
    {
        if (!$field->crudFieldOptions) {
            throw new Exception('Field options are not loaded');
        }

        if (!$field->crudFieldOptions->crud) {
            throw new Exception('Field options crud is not loaded');
        }

        $output = view('laravel11::migration', [
            'isNewTable' => $this->isNewTable || $field->created_at > $this->deployment->prev()?->created_at,
            'uses' => $this->generateUses(),
            'tableName' => $this->orderManyToManyName($crud, $field->crudFieldOptions->crud),
            'tableColumns' => $this->generateManyToManyColumns($field),
        ])->render();

        return '<?php' . PHP_EOL . PHP_EOL . $output;
    }

    public function getName(): string
    {

        if ($this->isNewTable) {
            $typePart = 'create';
            $datePart = $this->crud->created_at?->format('Y_m_d_his')
                ?? ('0000_00_00_' . str_pad((string)$this->crud->menu_order, 6, '0', STR_PAD_LEFT));
        } else {
            $typePart = 'update';
            $datePart = $this->deployment->created_at?->format('Y_m_d_his')
                ?? ('0000_00_00_' . str_pad((string)$this->crud->menu_order, 6, '0', STR_PAD_LEFT));
        }

        return "{$datePart}_{$typePart}_{$this->generateTableName()}_table";
    }

    public function getManyToManyName(CrudField $field, int $order, Crud $first, Crud $second): string
    {
        $namePart = $this->orderManyToManyName($first, $second);

        if ($this->isNewTable || $field->created_at > $this->deployment->prev()?->created_at) {
            $typePart = 'create';
            $datePart = $field->created_at?->format('Y_m_d_his')
                ?? ('0000_00_00_' . str_pad((string)$order, 6, '0', STR_PAD_LEFT));
        } else {
            $typePart = 'update';
            $datePart = $this->deployment->created_at?->format('Y_m_d_his')
                ?? ('0000_00_00_' . str_pad((string)$order, 6, '0', STR_PAD_LEFT));
        }

        return "{$datePart}_{$typePart}_{$namePart}_table";
    }

    private function generateUses(): string
    {
        return ''; // TODO: Implement this if needed - probably we can use a config option.
    }

    private function generateTableName(): string
    {
        return str($this->crud->title)->snake()->plural()->toString();
    }

    private function generateColumns(): string
    {
        $columns = [];

        $containsTimestampsColumns = false;
        $containsSoftDeleteColumns = false;

        $prevDate = $this->deployment->prev()?->created_at;

        if ($prevDate) {
            $this->deployment->addNewMessage('Prev deployment date: ' . $prevDate->format('Y-m-d H:i:s') . PHP_EOL);
            $fieldsToUpdate = $this->crud->fields()->where('updated_at', '>=', $prevDate)->get();
        } else {
            $this->deployment->addNewMessage('First deployment' . PHP_EOL);
            $fieldsToUpdate = $this->crud->fields;
        }

        /** @var CrudField $field */
        foreach ($fieldsToUpdate as $field) {
            if ($field->type === CrudFieldTypes::BELONGS_TO_MANY) {
                continue;
            }

            // TODO: This should be optional/configured in CRUD creations
            // If columns is present in this release we assume that you have enabled it now
            if (in_array($field->key, ['created_at', 'updated_at'])) {
                $containsTimestampsColumns = $field->created_at < $prevDate;
                continue;
            }
            if ($field->key == 'deleted_at') {
                $containsSoftDeleteColumns = $field->created_at < $prevDate;
                continue;
            }

            if ($prevDate && $field->created_at < $prevDate) {
                // Field to update
                $this->deployment->addNewMessage('Field to update: ' . $field->key . PHP_EOL);
                $fieldGenerator = RetrieveGeneratorForField::for($field);
                $columns[] = $this->indentString($fieldGenerator->getMigrationLine(true), 3);
            } else {
                // New Field
                $this->deployment->addNewMessage('Field to create: ' . $field->key . PHP_EOL);
                $fieldGenerator = RetrieveGeneratorForField::for($field);
                $columns[] = $this->indentString($fieldGenerator->getMigrationLine(), 3);
            }
        }

        if ($containsTimestampsColumns) {
            $columns[] = $this->indentString(
                (new MigrationLineGenerator())
                    ->setType('timestamps')
                    ->toString(),
                3);
        }

        if ($containsSoftDeleteColumns) {
            $columns[] = $this->indentString(
                (new MigrationLineGenerator())
                    ->setType('softDeletes')
                    ->toString(),
                3);
        }

        return implode(PHP_EOL, $columns);
    }

    protected function indentString(string $string, int $level = 1): string
    {
        return implode(
            PHP_EOL,
            array_map(
                static fn(string $line) => ($line !== '') ? (str_repeat('    ', $level) . $line) : '',
                explode(PHP_EOL, $string),
            ),
        );
    }

    private function generateManyToManyColumns(CrudField $field): string
    {
        $columns = [];

        // If we want to drop ID field from many-to-many relationships - just remove this array element
        $columns[] = $this->indentString(
            (new MigrationLineGenerator())
                ->setType('id')
                ->toString(),
            3);

        $fieldGenerator = RetrieveGeneratorForField::for($field);
        $columns[] = $this->indentString($fieldGenerator->getMigrationLine(), 3);

        return implode(PHP_EOL, $columns);
    }

    private function orderManyToManyName(Crud $first, Crud $second): string
    {
        $table_1 = str($first->title)->snake()->singular()->toString();
        $table_2 = str($second->title)->snake()->singular()->toString();

        // pivot table name should be in alphabetical order
        $pivotOrder = strcasecmp($table_1, $table_2);
        if ($pivotOrder < 0) {
            // table1 is first
            $name = $table_1 . '_' . $table_2;
        } elseif ($pivotOrder > 0) {
            // table2 is first
            $name = $table_2 . '_' . $table_1;
        } else {
            throw new Exception('Invalid pivot table names');
        }

        return $name;
    }

    /**
     * Test if this CRUD has any changes that need to be saved in a migration
     *
     * @return bool
     * @todo Improve the controls carried out in this method
     */
    public function hasChanges(): bool
    {
        return !empty($this->generateColumns());
    }
}
