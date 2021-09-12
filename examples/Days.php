<?php

namespace App\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * This defines a day type with all
 * seven days of the week, include 'none'
 * to allow setting an 'undefined' value.
 *
 * @method static self none() This is used to set 'null' day
 * @method static self mon() The first day of the week
 * @method static self tue() The second day of the week
 * @method static self wed() The third day of the week
 * @method static self thu() The fourth day of the week
 * @method static self fri() The fifth day of the week
 * @method static self sat() The sixth day of the week
 * @method static self sun() The last day of the week
 */
final class Days extends BaseEnumRoutable
{
    protected static function values(): array
    {
        return [
            'none' => 1,
            'mon' => 1 << 1,
            'tue' => 1 << 2,
            'wed' => 1 << 3,
            'thu' => 1 << 4,
            'fri' => 1 << 5,
            'sat' => 1 << 6,
            'sun' => 1 << 7,
        ];
    }

    protected static function labels(): array
    {
        return [
            'none' => 'None',
            'mon' => 'Monday',
            'tue' => 'Tuesday',
            'wed' => 'Wednesday',
            'thu' => 'Thursday',
            'fri' => 'Friday',
            'sat' => 'Saturday',
            'sun' => 'Sunday'
        ];
    }

    public function __toString(): string
    {
        return (string)$this->label;
    }

    /**
     * Checks if days contain this day
     *
     * @param int $days
     * @return boolean
     */
    public function has(int $days)
    {
        return (bool)($this->value & $days);
    }
}
