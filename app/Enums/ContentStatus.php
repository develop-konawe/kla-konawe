<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ContentStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case REVIEWED = 'reviewed';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::SUBMITTED => 'Diajukan',
            self::REVIEWED => 'Ditinjau',
            self::PUBLISHED => 'Diterbitkan',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'info',
            self::REVIEWED => 'warning',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'danger',
        };
    }
}
