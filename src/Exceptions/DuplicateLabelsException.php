<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown when your enum definition
 * contain multiple times the same label value
 *
 * This should only happen if you override the labels method
 */
class DuplicateLabelsException extends Exception
{
}
