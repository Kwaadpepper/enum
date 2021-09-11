<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * @method static self none()
 * @method static self mon()
 * @method static self tue()
 * @method static self wed()
 * @method static self thu()
 * @method static self fri()
 * @method static self sat()
 * @method static self sun()
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
