# Changelog

All notable changes to `Enum` will be documented in this file.

## **Version 2.1.1**

### Fixed

- Reintroduce getRouteKey to handle router with model + tests

## **Version 2.1.0**

### Added

- Full unit tests with 100% coverage
- A laravel command to generate an Enum ``php artisan make:enum`
- examples and a full, readme doc

### Changed

- Code refactoring for most of the code
- Removed unused code parts
- Set requirements for unit test to laravel 7
- translations are in php files

### Fixed

- An enum used as in a route will now provoque a 404 code instead of generating null enum
- CheckEnumRule nows works properly and has unit tests

## **Version 2.0.0**

### Changed

- Now if you are using enums in laravel, you have to extend from `BaseEnumRoutable` instead of `BaseEnum`. This will allow for enum to be used in routes as a parameter getting them as a controller parameter with implicit binding.
- All laravel dependencies have been removed, this allows enum to be used in another project type. The project is now dependency free.

## **Version 0.1.4**

### Added

- Validation rule EnumIsValidRule, the constructor needs a BaseEnum class
  eg: new EnumIsValidRule(ArcadiaMailType::class)

### Fixed

- Unit tests with 0.1.2 changes

## **Version 0.1.3**

### Added

- Validation rule EnumIsValidRule, the constructor needs a BaseEnum class
  eg: new EnumIsValidRule(ArcadiaMailType::class)

### Fixed

- Unit tests with 0.1.2 changes

## **Version 0.1.2**

### Changed

- Changed toJson to output array with label and value

## **Version 0.1.1**

### Fixed

- Fixed performances issue on toValues and toLabels

## **Version 0.1.0**

### - Changed

- toArray Method now returns an array of BaseEnums
  this is because php numeric string array indexes to int, this would break
  origin enum values

## **Version 0.0.2**

### - Add

- Enum cast in Models

    usage:
    Put this in your model

    use CastsEnums;

    protected $enumCasts = [
        'status' => OrderStatus::class // This is a class that extends \Kwaadpepper\Enum\BaseEnum
    ];

## **Version 0.0.1**

### Initial release
