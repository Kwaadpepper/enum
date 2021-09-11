<?php

namespace Kwaadpepper\Enum\Exceptions;

use Exception;

/**
 * Is thrown when your enum definition
 * contain multiple times the same value
 *
 * This should only happen if you override the values method
 */ class DuplicateValuesException extends Exception
{
  //
}
