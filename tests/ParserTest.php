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
