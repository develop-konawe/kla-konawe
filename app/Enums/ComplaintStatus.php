<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ComplaintStatus: string implements HasLabel, HasColor
{
    case New = 'new';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::New => 'Baru',
            self::InProgress => 'Diproses',
            self::Resolved => 'Selesai',
            self::Closed => 'Ditutup',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'danger',
            self::InProgress => 'warning',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }
}
