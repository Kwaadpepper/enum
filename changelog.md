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
