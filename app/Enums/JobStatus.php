<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
            self::Rejected => 'Rejected',
        };
    }
}
