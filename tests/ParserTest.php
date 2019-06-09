<?php

namespace Cerbero\LaravelEnum;

use PHPUnit\Framework\TestCase;

/**
 * The parser test.
 *
 */
class ParserTest extends TestCase
{
    /**
     * The parser to test.
     *
     * @var Parser
     */
    protected $parser;

    /**
     * Set up the test
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->parser = new Parser;
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneName()
    {
        $definition = 'ENUM_NAME';

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('ENUM_NAME', $enums[0]->name);
        $this->assertSame('enum_name', $enums[0]->key);
        $this->assertNull($enums[0]->value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneNameAndKey()
    {
        $definition = 'OTHER_NAME=foo';

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('OTHER_NAME', $enums[0]->name);
        $this->assertSame('foo', $enums[0]->key);
        $this->assertNull($enums[0]->value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneEnum()
    {
        $definition = 'NAME=2=[{"foo":"bar"},{"baz":1.2}]';
        $expectedValue = [
            ['foo' => 'bar'],
            ['baz' => 1.2],
        ];

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('NAME', $enums[0]->name);
        $this->assertSame(2, $enums[0]->key);
        $this->assertSame($expectedValue, $enums[0]->value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOnlyNames()
    {
        $definition = 'FIRST_NAME|SECOND_NAME|THIRD_NAME';
        $expectedNames = ['FIRST_NAME', 'SECOND_NAME', 'THIRD_NAME'];
        $expectedKeys = ['first_name', 'second_name', 'third_name'];

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(3, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertNull($enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseDefinitionWithOnlyNamesAndKeys()
    {
        $definition = 'ONE=1|TWO=false|THREE=["foo"]|FOUR=4.4';
        $expectedNames = ['ONE', 'TWO', 'THREE', 'FOUR'];
        $expectedKeys = [1, false, ['foo'], 4.4];

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(4, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertNull($enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseDefinitionWithManyEnums()
    {
        $definition = 'THE_ONE=11=1.3|THE_TWO=false=true|THE_THREE=["baz"]=22|THE_FOUR=40.4={"foo":"bar"}';
        $expectedNames = ['THE_ONE', 'THE_TWO', 'THE_THREE', 'THE_FOUR'];
        $expectedKeys = [11, false, ['baz'], 40.4];
        $expectedValues = [1.3, true, 22, ['foo' => 'bar']];

        $enums = $this->parser->parseDefinition($definition);

        $this->assertIsArray($enums);
        $this->assertCount(4, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertSame($expectedValues[$i], $enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneNamePassingKeys()
    {
        $definition = 'ENUM_NAME';
        $keys = $this->getFreshKeys(Keys::INT0);

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('ENUM_NAME', $enums[0]->name);
        $this->assertSame(0, $enums[0]->key);
        $this->assertNull($enums[0]->value);
    }

    /**
     * Retrieve a fresh instance of the given key
     *
     * @param string $key
     * @return \Cerbero\LaravelEnum\Keys
     */
    private function getFreshKeys(string $key) : Keys
    {
        $name = Keys::nameForKey($key);
        $map = (new Keys('', null, null))->map();
        $value = $map[$key];

        return new Keys($name, $key, $value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneNameAndValuePassingKeys()
    {
        $definition = 'OTHER_NAME=foo';
        $keys = $this->getFreshKeys(Keys::LOWER);

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('OTHER_NAME', $enums[0]->name);
        $this->assertSame('other_name', $enums[0]->key);
        $this->assertSame('foo', $enums[0]->value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOneEnumPassingKeys()
    {
        $definition = 'NAME=2=[{"foo":"bar"},{"baz":1.2}]';
        $keys = $this->getFreshKeys(Keys::INT1);
        $expectedValue = [
            ['foo' => 'bar'],
            ['baz' => 1.2],
        ];

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(1, $enums);
        $this->assertInstanceOf(EnumDefinition::class, $enums[0]);
        $this->assertSame('NAME', $enums[0]->name);
        $this->assertSame(1, $enums[0]->key);
        $this->assertSame($expectedValue, $enums[0]->value);
    }

    /**
     * @test
     */
    public function parseDefinitionWithOnlyNamesPassingKeys()
    {
        $definition = 'FIRST_NAME|SECOND_NAME|THIRD_NAME';
        $keys = $this->getFreshKeys(Keys::BITWISE);
        $expectedNames = ['FIRST_NAME', 'SECOND_NAME', 'THIRD_NAME'];
        $expectedKeys = [1, 2, 4];

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(3, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertNull($enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseDefinitionWithOnlyNamesAndValuesPassingKeys()
    {
        $definition = 'ONE=1|TWO=false|THREE=["foo"]|FOUR=4.4';
        $keys = $this->getFreshKeys(Keys::BITWISE);
        $expectedNames = ['ONE', 'TWO', 'THREE', 'FOUR'];
        $expectedKeys = [1, 2, 4, 8];
        $expectedValues = [1, false, ['foo'], 4.4];

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(4, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertSame($expectedValues[$i], $enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseDefinitionWithManyEnumsPassingKeys()
    {
        $definition = 'THE_ONE=11=1.3|THE_TWO=false=true|THE_THREE=["baz"]=22|THE_FOUR=40.4={"foo":"bar"}';
        $keys = $this->getFreshKeys(Keys::INT0);
        $expectedNames = ['THE_ONE', 'THE_TWO', 'THE_THREE', 'THE_FOUR'];
        $expectedKeys = [0, 1, 2, 3];
        $expectedValues = [1.3, true, 22, ['foo' => 'bar']];

        $enums = $this->parser->parseDefinition($definition, $keys);

        $this->assertIsArray($enums);
        $this->assertCount(4, $enums);

        for ($i = 0; $i < count($enums); $i++) {
            $this->assertInstanceOf(EnumDefinition::class, $enums[$i]);
            $this->assertSame($expectedNames[$i], $enums[$i]->name);
            $this->assertSame($expectedKeys[$i], $enums[$i]->key);
            $this->assertSame($expectedValues[$i], $enums[$i]->value);
        }
    }

    /**
     * @test
     */
    public function parseNullables()
    {
        $actual1 = $this->parser->parseValue(null);
        $actual2 = $this->parser->parseValue('');

        $this->assertNull($actual1);
        $this->assertNull($actual2);
    }

    /**
     * @test
     */
    public function parseIntegers()
    {
        $actual1 = $this->parser->parseValue(1);
        $actual2 = $this->parser->parseValue('1');

        $this->assertSame(1, $actual1);
        $this->assertSame(1, $actual2);
    }

    /**
     * @test
     */
    public function parseBooleans()
    {
        $actual1 = $this->parser->parseValue('true');
        $actual2 = $this->parser->parseValue('false');

        $this->assertTrue($actual1);
        $this->assertFalse($actual2);
    }

    /**
     * @test
     */
    public function parseFloats()
    {
        $actual1 = $this->parser->parseValue(1.1);
        $actual2 = $this->parser->parseValue('2.2');

        $this->assertSame(1.1, $actual1);
        $this->assertSame(2.2, $actual2);
    }

    /**
     * @test
     */
    public function parseArrays()
    {
        $actual1 = $this->parser->parseValue('["foo"]');
        $actual2 = $this->parser->parseValue('{"bar":"baz"}');

        $this->assertSame(['foo'], $actual1);
        $this->assertSame(['bar' => 'baz'], $actual2);
    }
}
