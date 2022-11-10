<?php

namespace Cerbero\LaravelEnum\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Cerbero\LaravelEnum\StubAssembler;
use Cerbero\LaravelEnum\Parser;
use Cerbero\LaravelEnum\Keys;
use Rexlabs\Enum\Exceptions\InvalidKeyException;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * The Artisan command to generate Enum classes.
 *
 */
class EnumMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:enum
                            {name : The name of the class or the relative path}
                            {cases : Either the enum names or values}
                            {--n|named= : The names to associate the enum values with}
                            {--b|backed= : The values to back the enum names}
                            {--k|keys= : The enum keys (color=red,blue,green isOdd=true,false,true)}
                            {--l|lang=en : The language to add translations to}
                            {--f|force : Create the enum even if it already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Enum';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/enum.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($path = $this->option('path')) {
            // Ensure the path starts with "app/"
            $path = Str::start(ltrim($path, '/'), 'app/');
            // Remove "app/" from the beginning of the path
            $path = preg_replace('#^app\/#', '', $path);
            // Convert the path into namespace
            $namespace = implode('\\', array_map('ucfirst', explode('/', $path)));
            // Prepend the root namespace
            return $rootNamespace . '\\' . $namespace;
        }

        return $rootNamespace . '\Enums';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $enums = $this->parseEnums();

        return (new StubAssembler($stub, $enums))
            ->replaceMethodTags()
            ->replaceConstants()
            ->replaceMap()
            ->getStub();
    }

    /**
     * Parse the command syntax and retrieve the enums
     *
     * @return array
     */
    private function parseEnums(): array
    {
        // Normalise definition as argument() may return an array
        $enum = (array) $this->argument('enum');
        $definition = trim($enum[0]);
        $keys = $this->getKeys();

        return (new Parser)->parseDefinition($definition, $keys);
    }

    /**
     * Retrieve the keys to generate
     *
     * @return \Cerbero\LaravelEnum\Keys|null
     */
    private function getKeys(): ?Keys
    {
        if (null === $key = $this->option('keys')) {
            return null;
        }

        // try {
        //     return Keys::instanceFromKey($key);
        // } catch (InvalidKeyException $e) {
        //     $keys = implode(', ', Keys::keys());
        //     throw new InvalidArgumentException("Invalid type provided for keys. Allowed keys: {$keys}");
        // }
    }
}
