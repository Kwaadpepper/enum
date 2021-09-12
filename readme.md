# Enum
![GitHub](https://img.shields.io/github/license/Kwaadpepper/enum?style=flat-square)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/Kwaadpepper/enum/PHP%20Composer?style=flat-square)

This package goal is to provide a complete solution to lack a long time php weakness : it do not have any `enum` support.
This will change with [**php 8 enums proposition**](https://php.watch/versions/8.1/enums). In the mean time we still a support for this allowing us to generate generic and extendable applications (instead of have singletons sql tables..).

You will be able to use enums in any project using a simple definition, take a look at example [Basic](examples/BasicEnum.php) or a [more sophisticated Example](examples/Days.php)

## Installation

Via Composer

``` bash
$ composer require kwaadpepper/enum
```

---

## Usage

**All enum have two properties : `label` and `value`, only value has to be unique. Each enum can have multiple options all written on class comment as static methods, these have to be unique !**

- On Any project, take a look on [examples](examples) to create an enum class.

1. **Invoke an enum value**
    ``` php
    Days::mon()
    ```

2. **Compare enums**
    ``` php
    Days::mon()->equals(Days::tue()) // false
    Days::mon()->equals(Days::mon()) // true
    Days::mon()->value === Days::tue()->value // false
    Days::mon()->value === Days::mon()->value // true
    ```

3. **Print an enum**
    ``` php
    echo Days::mon(); // 2
    echo Days::tue(); // 4
    echo Days::mon()->label; // Monday
    ```
    As you can see enum implements the __toString method which you can override to display the label instead of the value.
    This default behavior is set like this for a better behavior in laravel.

4. **Serialise an enum**
    ``` php
    echo json_encode(Days::mon()); // {"label":"Monday","value":2}
    ```
    Enums implement the JsonSerializable interface

- On a Laravel project you can use enums in multiple ways

1. **As a property**
   ``` php
   // Add theses to your model
    use CastsEnums;
    protected $enumCasts = [
        'day' => Days::class
    ];
   ```
   This will allow you model to store an enum in database using its value, and then cast the property to an enum when accessing it

2. **As a route parameter**

   Define a route like the following
   ``` php
   Route::get('/days/{day}', function (Days $day) {
            return response()->json([$day]);
        })->middleware('bindings');
    // OR
    Route::get('/days/{day}', [DayController::class, 'getDay']);
   ```
   Then on your controller for the `DayController` example
   ``` php
   public function getDay(Request $request, Day $day) {
       return response()->json([$day]);
   }
   ```

3. **As a request parameter using validation**

    Take a look at [this request example](tests/Requests/PassDayRequest.php).

    ``` php
    new EnumIsValidRule(Days::class)
    ```

    It use EnumIsValidRule to validate the parameter as being a valid enum value.
    
    **Note that you may still have to cast your parameter to int if your enum value is an int, as enum check is strict**

4. **As a model primary Key**

   Take a look at [unit test](tests/BaseEnumRoutableTest.php) using [the Report enum class from tests](tests/Models/Report.php)

---
## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

---

## Testing

``` bash
$ composer test
```
---
## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

---
## Security

If you discover any security related issues, please email github@jeremydev.ovh instead of using the issue tracker.

---
## Credits

- [Jérémy Munsch][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kwaadpepper/enum.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kwaadpepper/enum.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/kwaadpepper/enum/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/kwaadpepper/enum
[link-downloads]: https://packagist.org/packages/kwaadpepper/enum
[link-travis]: https://travis-ci.org/kwaadpepper/enum
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/kwaadpepper
[link-contributors]: ../../contributors
