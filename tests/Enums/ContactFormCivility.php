<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use Kwaadpepper\Enum\BaseEnumRoutable;

/**
 * @method static self m()
 * @method static self mme()
 * @method static self mlle()
 */
final class ContactFormCivility extends BaseEnumRoutable
{
    /**
     * Output the value as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->label;
    }
}
