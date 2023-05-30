<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * @method static self one()
 * @method static self two()
 * @method static self three()
 */
final class DuplicatedValues extends BaseEnumRoutable
{
    /**
     * All values
     *
     * @return array
     */
    protected static function values(): array
    {
        return [
            'one' => '0',
            'two' => '1',
            'three' => '1'
        ];
    }
}
