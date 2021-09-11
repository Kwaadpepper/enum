<?php

namespace Kwaadpepper\Enum\Tests;

use BadMethodCallException;
use Exception;
use Kwaadpepper\Enum\Exceptions\DuplicateLabelsException;
use Kwaadpepper\Enum\Exceptions\DuplicateValuesException;
use Kwaadpepper\Enum\Exceptions\UnknownEnumProperty;
use Kwaadpepper\Enum\Tests\Enums\Days;
use Kwaadpepper\Enum\Tests\Enums\DuplicatedLabels;
use Kwaadpepper\Enum\Tests\Enums\DuplicatedValues;
use Kwaadpepper\Enum\Tests\Enums\ForceStringsFromInteger;
use Kwaadpepper\Enum\Tests\Models\Report;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use stdClass;
use TypeError;

class BaseEnumTest extends TestCase
{
    public function testEnum()
    {
        $mon = Days::mon();
        $tue = Days::tue();
        Days::toArray();
        Days::toValues();
        Days::toLabels();
        json_encode($tue);

        // basic tests
        $this->assertEquals('Monday', $mon->label, 'Days::mon()->value should be equals to `Monday`');
        $this->assertEquals(2, $mon->value, 'Days::mon()->value should be equals to `2`');
        $this->assertClassHasAttribute('value', Days::class, 'Days should have a value property');
        $this->assertClassHasAttribute('label', Days::class, 'Days should have a label property');

        // undef prop test
        $exception = null;
        try {
            $mon->propthatdontexists;
        } catch (Exception $e) {
            $exception = $e;
        } finally {
            $this->assertEquals(
                UnknownEnumProperty::class,
                \is_object($exception) ? \get_class($exception) : $exception,
                'Accessing non existent prop on base enum should throw an UnknownEnumProperty exception'
            );
        }

        $this->assertEquals('Days', $mon->getEnumName(), 'getEnumName should return the enum class name');
        $this->assertEquals('mon', $mon->getDefinition(), 'Days::mon() definition should be `mon`');
        $this->assertFalse($mon->equals($tue), 'Days::mon() should not be equals to Days::tue()');
        $this->assertTrue($mon->equals($mon), 'Days::mon() should be equals to Days::mon()');
        $this->assertIsArray(Days::toArray(), 'toArray method should return an array');
        $this->assertIsArray(Days::toValues(), 'toValues method should return an array');
        $this->assertIsArray(Days::toLabels(), 'toLabels method should return an array');
        $this->assertEquals([
            0 => 1,
            1 => 2,
            2 => 4,
            3 => 8,
            4 => 16,
            5 => 32,
            6 => 64,
            7 => 128
        ], Days::toValues(), 'Days::toValues method should return an integer array');
        $this->assertEquals([
            0 => 'None',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ], Days::toLabels(), 'Days::toLabels method should return a string array');
        $this->assertEquals([
            0 => Days::none(),
            1 => Days::mon(),
            2 => Days::tue(),
            3 => Days::wed(),
            4 => Days::thu(),
            5 => Days::fri(),
            6 => Days::sat(),
            7 => Days::sun()
        ], Days::toArray(), 'Days::toLabels method should return an enum array');
        $this->assertEquals(
            (string)ForceStringsFromInteger::valueA()->value,
            (string)ForceStringsFromInteger::valueA(),
            'BaseEnum::__toString should return the value as a string'
        );
    }

    public function testCannotAssignAnEnumValue()
    {
        $this->expectError();
        $enum        = Days::mon();
        $enum->value = 'anything';
    }

    public function testCannotAssignAnEnumLabel()
    {
        $this->expectError();
        $enum        = Days::mon();
        $enum->label = 'anything';
    }

    /**
     * This method is here for code coverage
     *
     * @return void
     */
    public function testHackingEnumValueCouldConfuseCache()
    {
        $enum     = Days::mon();
        $refClass = new ReflectionClass($enum);
        $property = $refClass->getProperty('value');
        $property->setAccessible(true);
        $property->setValue($enum, 'anything');
        $this->assertEquals('anything', $enum->value);
        $this->assertNull(
            $enum->getDefinition(),
            'BaseEnum::getDefinition should be null when hacking var consing cache'
        );
    }

    public function testValidEnumSet()
    {
        $this->expectNotToPerformAssertions();
        $report      = new Report();
        $report->day = Days::mon();
        $report->day = Days::mon()->value;
    }

    public function testInvalidEnumSet()
    {
        $this->expectException(BadMethodCallException::class);
        $report      = new Report();
        $report->day = Days::mon()->label;
    }

    public function testInvalidEnumSetWithFloat()
    {
        $this->expectException(TypeError::class);
        $report      = new Report();
        $report->day = 0.1;
    }

    public function testInvalidEnumSetNotAuthorizedType()
    {
        $this->expectException(TypeError::class);
        $report      = new Report();
        $report->day = new stdClass();
    }

    public function testInvalidEnumCanSetNull()
    {
        $this->expectNotToPerformAssertions();
        $report      = new Report();
        $report->day = null;
    }

    public function testDuplicatedValues()
    {
        $this->expectException(DuplicateValuesException::class);
        DuplicatedValues::one();
    }

    public function testDuplicatedLabels()
    {
        $this->expectException(DuplicateLabelsException::class);
        DuplicatedLabels::one();
    }
}
