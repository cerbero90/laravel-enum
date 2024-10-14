<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\CasesCollection;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

it('dumps the cases', function() {
    $expectedDump = <<<OUT
array:3 [
  0 => Cerbero\LaravelEnum\BackedEnum {
    +name: "One"
    +value: 1
  }
  1 => Cerbero\LaravelEnum\BackedEnum {
    +name: "Two"
    +value: 2
  }
  2 => Cerbero\LaravelEnum\BackedEnum {
    +name: "Three"
    +value: 3
  }
]

OUT;

    $originalHandler = VarDumper::setHandler(function(mixed $var) {
        $clonedVar = (new VarCloner())->cloneVar($var)->withRefHandles(false);

        (new CliDumper('php://output'))->dump($clonedVar);
    });

    ob_start();

    (new CasesCollection(BackedEnum::cases()))->dump();

    try {
        expect(ob_get_clean())->toBe($expectedDump);
    } finally {
        VarDumper::setHandler($originalHandler);
    }
});
