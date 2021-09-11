<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnum;

/**
 * @method static self one()
 * @method static self two()
 * @method static self three()
 */
final class NotRoutableEnum extends BaseEnum
{
    protected static function values(): array
    {
        return [
            'one' => '0',
            'two' => '1',
            'three' => '2'
        ];
    }
}
