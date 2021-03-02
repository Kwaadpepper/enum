<?php

namespace Kwaadpepper\Enum\Tests;

use Kwaadpepper\Enum\Tests\Enums\ContactFormCivility;
use Kwaadpepper\Enum\Tests\Enums\Days;
use Kwaadpepper\Enum\Tests\Enums\ForceStringsFromInteger;
use Orchestra\Testbench\TestCase;

class EnumTest extends TestCase
{
    protected function getPackageAliases($app)
    {
        $app->alias(Days::class, 'Days');
        $app->alias(ContactFormCivility::class, 'ContactFormCivility');
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('/days/{day}', function (Days $day) {
            return response()->json([$day]);
        })->middleware('bindings');
        $router->get('/civilities/{civility}', function (ContactFormCivility $civility) {
            return response()->json([$civility]);
        })->middleware('bindings');
        $router->get('/force/{force}', function (ForceStringsFromInteger $force) {
            return response()->json([$force]);
        })->middleware('bindings');
    }


    public function testEnum()
    {
        $mon = Days::mon();
        $tue = Days::tue();
        Days::toArray();
        Days::toValues();
        Days::toLabels();
        json_encode($tue);
        $mon->getDefinition();
        $this->assertClassHasAttribute('value', Days::class);
        $this->assertClassHasAttribute('label', Days::class);
        $this->assertFalse($mon->equals($tue));
        $this->assertTrue($mon->equals($mon));
        $this->assertIsArray(Days::toArray());
        $this->assertIsArray(Days::toValues());
        $this->assertIsArray(Days::toLabels());
        $this->assertEquals([
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 8,
            5 => 16,
            6 => 32,
            7 => 64
        ], Days::toValues());
        $this->assertEquals([
            0 => 'None',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ], Days::toLabels());
        $this->assertEquals([
            0 => Days::none(),
            1 => Days::mon(),
            2 => Days::tue(),
            3 => Days::wed(),
            4 => Days::thu(),
            5 => Days::fri(),
            6 => Days::sat(),
            7 => Days::sun()
        ], Days::toArray());
        $this->assertEquals('Monday', (string)$mon);
    }

    public function testRoutesDays()
    {
        $response = $this->call('GET', '/days/1');
        $response->assertOk();
        $response->assertExactJson([Days::mon()]);
        $response = $this->call('GET', '/days/2');
        $response->assertOk();
        $response->assertExactJson([Days::tue()]);
    }

    public function testRoutesCivilities()
    {
        $response = $this->call('GET', '/civilities/mme');
        $response->assertOk();
        $response->assertExactJson([ContactFormCivility::mme()]);
    }

    public function testRoutesWithEnumIntString()
    {
        $response = $this->call('GET', '/force/1');
        $response->assertOk();
        $response->assertExactJson([ForceStringsFromInteger::valueB()]);
    }
}
