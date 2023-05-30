<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * @method static self one()
 * @method static self two()
 * @method static self three()
 */
final class DuplicatedLabels extends BaseEnumRoutable
{
    /**
     * All labels
     *
     * @return array
     */
    protected static function labels(): array
    {
        return [
            'one' => 'one',
            'two' => 'two',
            'three' => 'two'
        ];
    }
}
