<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum statusType: string implements HasLabel, HasColor
{
    case pending = 'Pending';
    case Approved = 'Approved';
    case Rejected = 'Rejected';

    public function getLabel(): ?string
    {
        return match($this) {
            self::pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
