# Changelog

All notable changes to `Enum` will be documented in this file.

## Version 0.0.1

### Initial release

## Version 0.0.2

### - Add
- Enum cast in Models

    usage:
    Put this in your model

    use CastsEnums;

    protected $enumCasts = [
        'status' => OrderStatus::class // This is a class that extends \Kwaadpepper\Enum\BaseEnum
    ];

## Version 0.1.0

### - Changed
- toArray Method now returns an array of BaseEnums
  this is because php numeric string array indexes to int, this would break
  origin enum values

## Version 0.1.1

### Fixed
  - fixed performances issue on toValues and toLabels

## Version 0.1.2

### Changed
  - changed toJson to output array with label and value
