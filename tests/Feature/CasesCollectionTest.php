<?php

use Cerbero\LaravelEnum\BackedEnum;
use Cerbero\LaravelEnum\BitwiseEnum;
use Cerbero\LaravelEnum\CasesCollection;
use Cerbero\LaravelEnum\PureEnum;
use Cerbero\LaravelEnum\User;
use Illuminate\Support\Facades\DB;
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

it('casts an unassigned property', function() {
    $user = new User();
    $user->save();

    expect($user->numbers)->toBeNull();
    expect(DB::table('users')->value('numbers'))->toBeNull();
});

it('casts a null value', function() {
    $user = new User(['numbers' => null]);
    $user->save();

    expect($user->numbers)->toBeNull();
    expect(DB::table('users')->value('numbers'))->toBeNull();
});

it('casts a bitwise', function() {
    $user = new User(['bitwise' => 3]);
    $user->save();

    expect($user->bitwise)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BitwiseEnum::Foo, BitwiseEnum::Bar]);

    expect(DB::table('users')->value('bitwise'))->toBe(3);
});

it('casts an array of bitwise values', function() {
    $user = new User(['bitwise' => [1, 2]]);
    $user->save();

    expect($user->bitwise)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BitwiseEnum::Foo, BitwiseEnum::Bar]);

    expect(DB::table('users')->value('bitwise'))->toBe(3);
});

it('casts an array of bitwise cases', function() {
    $user = new User(['bitwise' => [BitwiseEnum::Foo, BitwiseEnum::Bar]]);
    $user->save();

    expect($user->bitwise)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BitwiseEnum::Foo, BitwiseEnum::Bar]);

    expect(DB::table('users')->value('bitwise'))->toBe(3);
});

it('casts an array of names', function() {
    $user = new User(['pureNumbers' => ['Three', 'One']]);
    $user->save();

    expect($user->pureNumbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::Three, PureEnum::One]);

    expect(DB::table('users')->value('pureNumbers'))->toBe('["Three","One"]');
});

it('casts an array of values', function() {
    $user = new User(['numbers' => [3, 1]]);
    $user->save();

    expect($user->numbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::Three, BackedEnum::One]);

    expect(DB::table('users')->value('numbers'))->toBe('[3,1]');
});

it('casts an array of pure cases', function() {
    $user = new User(['pureNumbers' => [PureEnum::Three, PureEnum::One]]);
    $user->save();

    expect($user->pureNumbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::One, PureEnum::Three]);

    expect(DB::table('users')->value('pureNumbers'))->toBe('["One","Three"]');
});

it('casts an array of backed cases', function() {
    $user = new User(['numbers' => [BackedEnum::Three, BackedEnum::One]]);
    $user->save();

    expect($user->numbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::One, BackedEnum::Three]);

    expect(DB::table('users')->value('numbers'))->toBe('[1,3]');
});

it('casts unique pure cases', function() {
    $user = new User(['pureNumbers' => [PureEnum::Three, PureEnum::Three, PureEnum::One, PureEnum::One]]);
    $user->save();

    expect($user->pureNumbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::One, PureEnum::Three]);

    expect(DB::table('users')->value('pureNumbers'))->toBe('["One","Three"]');
});

it('casts unique backed cases', function() {
    $user = new User(['numbers' => [BackedEnum::Three, BackedEnum::Three, BackedEnum::One, BackedEnum::One]]);
    $user->save();

    expect($user->numbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::One, BackedEnum::Three]);

    expect(DB::table('users')->value('numbers'))->toBe('[1,3]');
});

it('casts unique case names', function() {
    $user = new User(['pureNumbers' => ['Three', 'Three', 'One', 'One']]);
    $user->save();

    expect($user->pureNumbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::Three, PureEnum::One]);

    expect(DB::table('users')->value('pureNumbers'))->toBe('["Three","One"]');
});

it('casts unique case values', function() {
    $user = new User(['numbers' => [3, 3, 1, 1]]);
    $user->save();

    expect($user->numbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::Three, BackedEnum::One]);

    expect(DB::table('users')->value('numbers'))->toBe('[3,1]');
});

it('casts a collection of pure cases', function() {
    $collection = new CasesCollection([PureEnum::Two, PureEnum::One]);
    $user = new User(['pureNumbers' => $collection]);
    $user->save();

    expect($user->pureNumbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::Two, PureEnum::One]);

    expect(DB::table('users')->value('pureNumbers'))->toBe('["Two","One"]');
});

it('casts a collection of backed cases', function() {
    $collection = new CasesCollection([BackedEnum::Two, BackedEnum::One]);
    $user = new User(['numbers' => $collection]);
    $user->save();

    expect($user->numbers)
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::Two, BackedEnum::One]);

    expect(DB::table('users')->value('numbers'))->toBe('[2,1]');
});

it('casts an invalid object', function() {
    $user = new User(['numbers' => new \stdClass]);
    $user->save();

    expect($user->numbers)->toBeNull();
    expect(DB::table('users')->value('numbers'))->toBeNull();
});

it('casts an invalid value', function() {
    $user = new User(['numbers' => 12.3]);
    $user->save();

    expect($user->numbers)->toBeNull();
    expect(DB::table('users')->value('numbers'))->toBeNull();
});

it('fails to cast invalid cases', fn() => (new User())->forceFill(['numbers' => [123]])->numbers)
    ->throws(ValueError::class, '123 is not a valid backing value for enum "Cerbero\LaravelEnum\BackedEnum"');

it('fails to cast an invalid enum', fn() => (new User())->forceFill(['invalid' => [1]])->numbers)
    ->throws(InvalidArgumentException::class, 'The cast argument must be a valid enum');
