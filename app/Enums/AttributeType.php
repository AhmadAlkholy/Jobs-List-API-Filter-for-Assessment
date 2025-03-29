<?php

namespace App\Enums;

enum AttributeType: string
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Date = 'date';
    case Select = 'select';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::Text => 'Text Input',
            self::Number => 'Number',
            self::Boolean => 'Yes/No',
            self::Date => 'Date',
            self::Select => 'Select'
        };
    }
    
    public static function random(): self
    {
        return self::cases()[array_rand(self::cases())];
    }
}
