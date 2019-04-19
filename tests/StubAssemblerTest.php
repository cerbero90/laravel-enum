<?php

namespace Cerbero\LaravelEnum;

use PHPUnit\Framework\TestCase;

/**
 * The stub assembler test.
 *
 */
class StubAssemblerTest extends TestCase
{
    /**
     * @test
     */
    public function replacePartsOfTheStub()
    {
        $enum1 = tap(new EnumDefinition, function (EnumDefinition $enumDefinition) {
            $enumDefinition->name = 'FIRST_ENUM';
            $enumDefinition->key = 'first_enum';
        });

        $enum2 = tap(new EnumDefinition, function (EnumDefinition $enumDefinition) {
            $enumDefinition->name = 'SECOND_ENUM';
            $enumDefinition->key = 1;
            $enumDefinition->value = true;
        });

        $enum3 = tap(new EnumDefinition, function (EnumDefinition $enumDefinition) {
            $enumDefinition->name = 'THIRD_ENUM';
            $enumDefinition->key = ['foo' => 'bar'];
            $enumDefinition->value = ['foo' => ['bar' => 'baz']];
        });

        $expected = file_get_contents(__DIR__ . '/expected.stub');
        $stub = file_get_contents(__DIR__ . '/../stubs/enum.stub');
        $actual = (new StubAssembler($stub, [$enum1, $enum2, $enum3]))
            ->replaceMethodTags()
            ->replaceConstants()
            ->replaceMap()
            ->getStub();

        $this->assertSame($expected, $actual);
    }
}
