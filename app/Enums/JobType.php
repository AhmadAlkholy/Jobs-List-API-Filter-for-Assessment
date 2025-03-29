<?php

namespace App\Enums;

enum JobType: string
{
    case FullTime = 'full-time';
    case PartTime = 'part-time';
    case Contract = 'contract';
    case Freelance = 'freelance';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::FullTime => 'Full Time',
            self::PartTime => 'Part Time',
            self::Contract => 'Contract',
            self::Freelance => 'Freelance',
            self::Internship => 'Internship',
        };
    }
}
