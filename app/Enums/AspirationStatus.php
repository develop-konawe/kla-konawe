<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AspirationStatus: string implements HasLabel, HasColor
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'Review',
            self::Published => 'Dipublikasikan',
            self::Rejected => 'Ditolak',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Review => 'warning',
            self::Published => 'success',
            self::Rejected => 'danger',
        };
    }
}
