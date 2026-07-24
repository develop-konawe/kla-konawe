<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PublicationClassification: string implements HasLabel, HasColor
{
    case PUBLIC = 'public';
    case INTERNAL = 'internal';
    case RESTRICTED = 'restricted';
    case SECRET = 'secret';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PUBLIC => 'Publik',
            self::INTERNAL => 'Internal',
            self::RESTRICTED => 'Terbatas',
            self::SECRET => 'Rahasia',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PUBLIC => 'success',
            self::INTERNAL => 'info',
            self::RESTRICTED => 'warning',
            self::SECRET => 'danger',
        };
    }
}
