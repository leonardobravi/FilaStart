<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CustomTraitTypes: string implements HasLabel
{
    case ANY = 'any';
    case MODEL = 'model';
    case RESOURCE = 'resource';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ANY => 'Any',
            self::MODEL => 'Model',
            self::RESOURCE => 'Resource',
        };
    }
}
