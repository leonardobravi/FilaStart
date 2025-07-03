<?php

namespace App\Models;

use App\Enums\CrudTypes;
use App\Enums\CustomTraitTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property CrudTypes $type
 * @property string $visual_title
 * @property string $namespace
 * @property string $alias
 * @property-read  string $class_name
 */
class CustomTrait extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'panel_id',
        'user_id',
        'type',
        'namespace',
        'alias',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'type' => CustomTraitTypes::class,
        ];
    }

    protected static function booted(): void
    {
        self::creating(static function (CustomTrait $customTrait) {
            if (!empty($customTrait->alias)) {
                str($customTrait->alias)
                    ->lower()
                    ->camel()
                    ->singular()
                    ->ucfirst()
                    ->toString();
            }

            if (!$customTrait->visual_title) {
                $customTrait->visual_title = str($customTrait->namespace)
                        ->camel()
                        ->singular()
                        ->ucfirst()
                        ->toString()
                    . ($customTrait->alias ? " AS $customTrait->alias" : '');
            }
        });
    }

    public function panel(): BelongsTo
    {
        return $this->belongsTo(Panel::class);
    }

    public function cruds(): BelongsToMany
    {
        return $this->belongsToMany(Crud::class, 'crud_custom_traits');
    }

    /**
     * Custom attribute to retrieve the short name of the trait
     *
     * @return string
     */
    public function getClassNameAttribute(): string
    {
        return $this->alias ?? basename(str($this->namespace)
            ->replace(['\\\\', '\\'], ['\\', DIRECTORY_SEPARATOR])
            ->toString());
    }
}
