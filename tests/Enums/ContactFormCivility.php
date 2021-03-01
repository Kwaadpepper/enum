<?php

namespace Kwaadpepper\Enum\Tests\Enums;

use BadMethodCallException;
use Kwaadpepper\Enum\BaseEnum;

/**
 * @method static self m()
 * @method static self mme()
 * @method static self mlle()
 */
final class ContactFormCivility extends BaseEnum
{
    public function __toString(): string
    {
        return (string) $this->label;
    }
}
