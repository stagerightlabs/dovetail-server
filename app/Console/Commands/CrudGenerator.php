<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;

class CrudGenerator extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {resource}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD scaffolding for a new resource';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Make a Model
        $this->createModel();

        // Make a migration
        $this->createMigration();

        // Make a Controller
        $this->createController();

        // Make a Resource
        $this->createResource();

        // Make a Test
        $this->createTest();

        // Make a Factory
        $this->createFactory();

        // Output the routes
        $this->printRoutes();
    }

    /**
     * Required by the GeneratorCommand class
     *
     * @return void
     */
    public function getStub()
    {
        //..
    }

    /**
     * Generate a migration for this resource
     *
     * @return void
     */
    public function createMigration()
    {
        $this->call('make:migration', [
            'name' => "create_{$this->plural()}_table",
            '--create' => $this->plural(),
        ]);
    }

    /**
     * Generate a model for this resource
     *
     * @return void
     */
    public function createModel()
    {
        // Derive the correct namespace
        $name = $this->qualifyClass($this->singular());

        // Determine the file path
        $path = $this->getPath($name);

        // Ensure this model doesn't already exist
        if ($this->alreadyExists($path)) {
            $this->error('Model already exists!');
            return false;
        }

        // Ensure the path is reachable
        $this->makeDirectory($path);

        // Fetch out stubbed file template
        $stub = $this->files->get($this->stubPath('model'));

        // Replace stubbed placeholders with real values
        $model =  $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        // Write the file to disk
        $this->files->put($path, $model);

        $this->info('Model created successfully.');
    }

    /**
     * Create a controller for this resource
     *
     * @return void
     */
    public function createController()
    {
        // Derive the correct namespace
        $name = $this->qualifyClass("Http/Controllers/{$this->singular()}Controller");

        // Determine the file path
        $path = $this->getPath($name);

        // Ensure this controller doesn't already exist
        if ($this->alreadyExists($path)) {
            $this->error('Controller already exists!');
            return false;
        }

        // Ensure the path is reachable
        $this->makeDirectory($path);

        // Fetch out stubbed file template
        $stub = $this->files->get($this->stubPath('controller'));

        // Replace stubbed placeholders with real values
        $controller = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $replacements = [
            'DummyFullModelClass' => $this->singular(),
            'DummyModelClass' => class_basename($this->singular()),
            'DummyModelVariablePlural' => lcfirst($this->plural()),
            'DummyModelVariable' => lcfirst($this->singular()),
        ];

        $controller = str_replace(array_keys($replacements), array_values($replacements), $controller);

        // Write the file to disk
        $this->files->put($path, $controller);

        $this->info('Controller created successfully.');
    }

    /**
     * Create an "API Resource" for this resource
     *
     * @return void
     */
    public function createResource()
    {
        $this->call('make:resource', [
            'name' => $this->singular() . "Resource",
        ]);
    }

    /**
     * Create a test fixture factory for this resource
     *
     * @return void
     */
    public function createFactory()
    {
        $this->call('make:factory', [
            'name' => $this->singular() . 'Factory',
            '--model' => $this->singular(),
        ]);
    }

    /**
     * Create tests for this resource
     *
     * @return void
     */
    public function createTest()
    {
        // Derive the correct namespace
        $name = str_after($this->qualifyClass("Tests/Feature/{$this->singular()}Test"), 'App\\');

        // Determine the file path
        $path = base_path(str_replace('\\', '/', lcfirst($name)).'.php');

        // Ensure this test doesn't already exist
        if ($this->alreadyExists($path)) {
            $this->error('Test already exists!');
            return false;
        }

        // Ensure the path is reachable
        $this->makeDirectory($path);

        // Fetch out stubbed file template
        $stub = $this->files->get($this->stubPath('test'));

        // Replace stubbed placeholders with real values
        $test = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $replacements = [
            'DummyFullModelClass' => $this->singular(),
            'DummyModelClass' => class_basename($this->singular()),
            'DummyModelVariablePlural' => lcfirst($this->plural()),
            'DummyModelVariable' => lcfirst($this->singular()),
        ];

        $test = str_replace(array_keys($replacements), array_values($replacements), $test);

        // Write the file to disk
        $this->files->put($path, $test);

        $this->info('Test created successfully.');
    }

    /**
     * Print routes for this resource to the screen
     *
     * @return void
     */
    public function printRoutes()
    {
        $routes = [
            "Route::get('/plural', 'SingularController@index')->name('plural.index');",
            "Route::post('/plural', 'SingularController@store')->name('plural.store');",
            "Route::get('/plural/{hashid}', 'SingularController@show')->name('plural.show');",
            "Route::put('/plural/{hashid}', 'SingularController@update')->name('plural.update');",
            "Route::delete('/plural/{hashid}', 'SingularController@delete')->name('plural.delete');",
        ];

        $this->info("Routes:");

        foreach ($routes as $route) {
            $this->info(str_replace(['plural', 'Singular'], [$this->plural(), $this->singular()], $route));
        }
    }

    /**
     * The singular name of this resource, uppercase
     *
     * @return string
     */
    protected function singular()
    {
        return Str::studly(class_basename(trim($this->argument('resource'))));
    }

    /**
     * The plural name of this resource, lowercase
     *
     * @return string
     */
    protected function plural()
    {
        return Str::plural(Str::snake(class_basename($this->argument('resource'))));
    }

    /**
     * Does a file exist?
     *
     * @param string $path
     * @return bool
     */
    public function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * Return the path to a stub file
     *
     * @param string $key
     * @return void
     */
    public function stubPath($key)
    {
        return app_path("Console/Stubs/{$key}.stub");
    }
}
