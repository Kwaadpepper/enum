<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * @method static self valueA()
 * @method static self valueB()
 * @method static self valueC()
 */
final class ForceStringsFromInteger extends BaseEnumRoutable
{
    /**
     * All values
     *
     * @return array
     */
    protected static function values(): array
    {
        return [
            'valueA' => '0',
            'valueB' => '1',
            'valueC' => '2'
        ];
    }
}
