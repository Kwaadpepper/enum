<?php

namespace Kwaadpepper\Enum\Tests;

use App\Http\Requests\PassDayRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kwaadpepper\Enum\Exceptions\EnumNotRoutableException;
use Kwaadpepper\Enum\Exceptions\UnknownEnumClass;
use Kwaadpepper\Enum\Rules\EnumIsValidRule;
use Kwaadpepper\Enum\Tests\Enums\ContactFormCivility;
use Kwaadpepper\Enum\Tests\Enums\Days;
use Kwaadpepper\Enum\Tests\Enums\ForceStringsFromInteger;
use Kwaadpepper\Enum\Tests\Enums\NotRoutableEnum;
use Kwaadpepper\Enum\Tests\Models\Alarm;
use Kwaadpepper\Enum\Tests\Models\Journal;
use Kwaadpepper\Enum\Tests\Models\Report;
use Orchestra\Testbench\TestCase;
use SplFileObject;

class BaseEnumRoutableTest extends TestCase
{
    /**
     * Defines aliases
     *
     * @param [type] $app
     * @return void
     */
    protected function getPackageAliases($app)
    {
        $app->alias(Alarm::class, 'Alarm');
        $app->alias(Journal::class, 'Journal');
        $app->alias(Report::class, 'Report');
        $app->alias(Days::class, 'Days');
        $app->alias(ContactFormCivility::class, 'ContactFormCivility');
        $app->alias(ForceStringsFromInteger::class, 'ForceStringsFromInteger');
        $app->alias(NotRoutableEnum::class, 'NotRoutableEnum');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
    protected function getPackageProviders($app)
    {
        return [
            'Kwaadpepper\Enum\EnumServiceProvider',
        ];
    }


    /**
     * Defines migrations
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('day');
        });
        Schema::create('alarms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('day');
        });
        Schema::create('journals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('day');
        });
    }

    /**
     * Defines seeders
     *
     * @return void
     */
    protected function defineDatabaseSeeders()
    {
        Report::create(['day' => Days::none()]);
        Report::create(['day' => Days::mon()]);
        Report::create(['day' => Days::tue()]);
        Report::create(['day' => Days::wed()]);
        Report::create(['day' => Days::thu()]);
        Report::create(['day' => Days::fri()]);
        Report::create(['day' => Days::sat()]);
        Report::create(['day' => Days::sun()]);
        Alarm::create(['day' => Days::none()]);
        Alarm::create(['day' => Days::mon()]);
        Alarm::create(['day' => Days::tue()]);
        Alarm::create(['day' => Days::wed()]);
        Alarm::create(['day' => Days::thu()]);
        Alarm::create(['day' => Days::fri()]);
        Alarm::create(['day' => Days::sat()]);
        Alarm::create(['day' => Days::sun()]);
        Journal::create(['day' => Days::none()]);
        Journal::create(['day' => Days::mon()]);
        Journal::create(['day' => Days::tue()]);
        Journal::create(['day' => Days::wed()]);
        Journal::create(['day' => Days::thu()]);
        Journal::create(['day' => Days::fri()]);
        Journal::create(['day' => Days::sat()]);
        Journal::create(['day' => Days::sun()]);
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
        $router->get('/journals/{journal}', function (Journal $journal) {
            return response()->json([$journal->day]);
        })->middleware('bindings');
        $router->get('/reports/{report}', function (Report $report) {
            return response()->json([$report->day]);
        })->middleware('bindings');
        $router->get('/alarms/{alarm}', function (Alarm $alarm) {
            return response()->json([$alarm->day]);
        })->middleware('bindings');
        $router->get('/days/{day}', function (Days $day) {
            return response()->json([$day]);
        })->middleware('bindings');
        $router->get('/day-validation', function (PassDayRequest $request) {
            return response()->json([Days::make((int)$request->day)]);
        })->middleware('bindings');
        $router->get('/civilities/{civility}', function (ContactFormCivility $civility) {
            return response()->json([$civility]);
        })->middleware('bindings');
        $router->get('/force/{force}', function (ForceStringsFromInteger $force) {
            return response()->json([$force]);
        })->middleware('bindings');
        $router->get('/notroutable/{notroutable}', function (NotRoutableEnum $notroutable) {
            dd($notroutable);
            return response()->json([$notroutable]);
        })->middleware('bindings');
    }

    public function testRoutesDays()
    {
        $response = $this->json('GET', sprintf('/days/%s', Days::none()->value));
        $response->assertOk();
        $response->assertExactJson([Days::none()]);
        $response = $this->json('GET', sprintf('/days/%s', Days::mon()->value));
        $response->assertOk();
        $response->assertExactJson([Days::mon()]);
    }

    public function testRoutesCivilities()
    {
        $response = $this->json('GET', sprintf('/civilities/%s', ContactFormCivility::mme()->value));
        $response->assertOk();
        $response->assertExactJson([ContactFormCivility::mme()]);
    }

    public function testRoutesWithEnumIntString()
    {
        $response = $this->json('GET', sprintf('/force/%s', ForceStringsFromInteger::valueB()->value));
        $response->assertOk();
        $response->assertExactJson([ForceStringsFromInteger::valueB()]);
    }

    public function testRoutesWithEnumNotFound()
    {
        $response = $this->json('GET', '/force/6');
        $response->assertNotFound();
    }

    public function testRoutesWithEnumAsPrimaryKey()
    {
        $response = $this->json('GET', sprintf('/reports/%s', Days::none()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/reports/%s', Days::mon()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/reports/%s', Days::tue()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/reports/%s', Days::fri()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/reports/%s', 9999));
        $response->assertNotFound();
    }

    public function testRoutesWithEnumAsPrimaryKeyWithoutCastEnums()
    {
        $response = $this->json('GET', sprintf('/alarms/%s', Days::none()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/alarms/%s', Days::mon()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/alarms/%s', Days::tue()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/alarms/%s', Days::fri()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/alarms/%s', 9999));
        $response->assertNotFound();
    }

    public function testRoutesWithEnumAsPrimaryKeyWithoutCastEnumsAndCast()
    {
        $response = $this->json('GET', sprintf('/journals/%s', Days::none()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/journals/%s', Days::mon()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/journals/%s', Days::tue()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/journals/%s', Days::fri()->value));
        $response->assertOk();
        $response = $this->json('GET', sprintf('/journals/%s', 9999));
        $response->assertNotFound();
    }

    public function testRules()
    {
        $rule = new EnumIsValidRule(Days::class);
        $this->assertTrue($rule->passes('day', Days::mon()->value));
        $this->assertFalse($rule->passes('day', 9999));
        $this->assertFalse($rule->passes('day', Days::mon()));
    }

    public function testRulesRequest()
    {
        $response = $this->json('GET', sprintf('/day-validation?day=%s', Days::fri()->value));
        $response->assertOk();
    }

    public function testRulesRequestFail()
    {
        $response = $this->json('GET', sprintf('/day-validation?day=%s', '"!:'));
        $response->assertJsonValidationErrors('day');
    }

    public function testRulesRequestInvalid()
    {
        $this->expectException(UnknownEnumClass::class);
        new EnumIsValidRule(SplFileObject::class);
    }

    public function testNotRoutableEnum()
    {
        $response = $this->json('GET', sprintf('/notroutable/%s', '1'));
        $response->assertStatus(500);
        $this->assertEquals(
            new EnumNotRoutableException(),
            $response->exception
        );
    }
}
