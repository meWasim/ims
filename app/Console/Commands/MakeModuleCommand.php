<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module
                            {name? : The module name}
                            {--force : Overwrite existing files}';

    protected $description = 'Create a complete ERP module architecture';

    public function handle(): int
    {
        /*
        |--------------------------------------------------------------------------
        | Module Information
        |--------------------------------------------------------------------------
        */

        $name = $this->getModuleName();

        if ($name === null) {
            return self::FAILURE;
        }

        $scope = $this->getScope();

        $description = $this->ask(
            'Module description',
            "Manage {$name}"
        );

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $this->showSummary(
            $name,
            $scope,
            $description
        );

        if (! $this->confirm(
            'Do you want to create this module?',
            true
        )) {
            $this->warn('Module creation cancelled.');

            return self::SUCCESS;
        }

        /*
        |--------------------------------------------------------------------------
        | Module Path / Namespace
        |--------------------------------------------------------------------------
        */

        $modulePath = app_path(
            "Modules/{$scope}/{$name}"
        );

        $namespace = "App\\Modules\\{$scope}\\{$name}";

        /*
        |--------------------------------------------------------------------------
        | Existing Module
        |--------------------------------------------------------------------------
        */

        if (
            File::exists($modulePath)
            && ! $this->option('force')
        ) {
            $this->error(
                "Module [{$scope}/{$name}] already exists."
            );

            $this->line(
                'Use --force if you want to overwrite existing files.'
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | Creating Module
        |--------------------------------------------------------------------------
        */

        $this->newLine();

        $this->components->info(
            "Creating {$scope} module: {$name}"
        );

        $this->newLine();

        /*
        |--------------------------------------------------------------------------
        | Core Architecture
        |--------------------------------------------------------------------------
        */

        $this->createDirectories($modulePath);

        $this->createContracts(
            $modulePath,
            $namespace,
            $name
        );

        $this->createDTOs(
            $modulePath,
            $namespace,
            $name
        );

        $this->createModel(
            $modulePath,
            $namespace,
            $name
        );

        $this->createRepository(
            $modulePath,
            $namespace,
            $name
        );

        $this->createService(
            $modulePath,
            $namespace,
            $name
        );

        $this->createRequests(
            $modulePath,
            $namespace,
            $name
        );

        $this->createResource(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Controllers
        |--------------------------------------------------------------------------
        */

        $this->createWebController(
            $modulePath,
            $namespace,
            $name
        );

        $this->createApiController(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Livewire
        |--------------------------------------------------------------------------
        */

        $this->createLivewire(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        $this->createViews(
            $modulePath,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Routes
        |--------------------------------------------------------------------------
        */

        $this->createRoutes(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Service Provider
        |--------------------------------------------------------------------------
        */

        $this->createProvider(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Configuration
        |--------------------------------------------------------------------------
        */

        $this->createConfig(
            $modulePath,
            $namespace,
            $name,
            $scope,
            $description
        );

        /*
        |--------------------------------------------------------------------------
        | Database
        |--------------------------------------------------------------------------
        */

        $this->createMigration(
            $modulePath,
            $name
        );

        $this->createSeeder(
            $modulePath,
            $namespace,
            $name
        );

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        $this->showSuccess(
            $name,
            $scope,
            $description,
            $namespace,
            $modulePath
        );

        return self::SUCCESS;
    }

    /*
    |--------------------------------------------------------------------------
    | Module Information
    |--------------------------------------------------------------------------
    */

    private function getModuleName(): ?string
    {
        $name = $this->argument('name');

        if (! $name) {
            $name = $this->ask(
                'What is the module name?'
            );
        }

        if (! $name) {
            $this->error(
                'Module name is required.'
            );

            return null;
        }

        return Str::studly($name);
    }

    private function getScope(): string
    {
        return $this->choice(
            'Where should this module belong?',
            [
                'Central',
                'Tenant',
            ],
            0
        );
    }

    private function showSummary(
        string $name,
        string $scope,
        string $description
    ): void {
        $this->newLine();

        $this->table(
            ['Property', 'Value'],
            [
                ['Name', $name],
                ['Scope', $scope],
                ['Description', $description],
                ['Status', 'Enabled'],
            ]
        );

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | Directories
    |--------------------------------------------------------------------------
    */

    private function createDirectories(
        string $modulePath
    ): void {
        $directories = [
            'Controllers/Web',
            'Controllers/Api',
            'Contracts',
            'DTOs',
            'Models',
            'Repositories',
            'Requests',
            'Resources',
            'Routes',
            'Services',
            'Providers',
            'Livewire',
            'Views',
            'Config',
            'Database/Migrations',
            'Database/Seeders',
        ];

        foreach ($directories as $directory) {
            File::ensureDirectoryExists(
                "{$modulePath}/{$directory}"
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Contracts
    |--------------------------------------------------------------------------
    */

    private function createContracts(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Contracts;

use {$namespace}\DTOs\Create{$name}DTO;
use {$namespace}\DTOs\Update{$name}DTO;

interface {$name}RepositoryInterface
{
    public function all();

    public function find(
        int|string \$id
    );

    public function create(
        Create{$name}DTO \$dto
    );

    public function update(
        int|string \$id,
        Update{$name}DTO \$dto
    );

    public function delete(
        int|string \$id
    ): bool;
}

PHP;

        $this->writeFile(
            "{$path}/Contracts/{$name}RepositoryInterface.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DTOs
    |--------------------------------------------------------------------------
    */

    private function createDTOs(
        string $path,
        string $namespace,
        string $name
    ): void {
        $create = <<<PHP
<?php

namespace {$namespace}\DTOs;

final readonly class Create{$name}DTO
{
    public function __construct(
        public array \$data,
    ) {
    }

    public static function fromArray(
        array \$data
    ): self {
        return new self(\$data);
    }

    public function toArray(): array
    {
        return \$this->data;
    }
}

PHP;

        $update = <<<PHP
<?php

namespace {$namespace}\DTOs;

final readonly class Update{$name}DTO
{
    public function __construct(
        public array \$data,
    ) {
    }

    public static function fromArray(
        array \$data
    ): self {
        return new self(\$data);
    }

    public function toArray(): array
    {
        return \$this->data;
    }
}

PHP;

        $this->writeFile(
            "{$path}/DTOs/Create{$name}DTO.php",
            $create
        );

        $this->writeFile(
            "{$path}/DTOs/Update{$name}DTO.php",
            $update
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    */

    private function createModel(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    use HasFactory;

    protected \$guarded = [];
}

PHP;

        $this->writeFile(
            "{$path}/Models/{$name}.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    */

    private function createRepository(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Repositories;

use {$namespace}\Contracts\\{$name}RepositoryInterface;
use {$namespace}\DTOs\Create{$name}DTO;
use {$namespace}\DTOs\Update{$name}DTO;
use {$namespace}\Models\\{$name};

class {$name}Repository implements {$name}RepositoryInterface
{
    public function all()
    {
        return {$name}::query()->get();
    }

    public function find(
        int|string \$id
    ) {
        return {$name}::query()
            ->findOrFail(\$id);
    }

    public function create(
        Create{$name}DTO \$dto
    ) {
        return {$name}::query()->create(
            \$dto->toArray()
        );
    }

    public function update(
        int|string \$id,
        Update{$name}DTO \$dto
    ) {
        \$model = \$this->find(\$id);

        \$model->update(
            \$dto->toArray()
        );

        return \$model->refresh();
    }

    public function delete(
        int|string \$id
    ): bool {
        return (bool) \$this
            ->find(\$id)
            ->delete();
    }
}

PHP;

        $this->writeFile(
            "{$path}/Repositories/{$name}Repository.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    */

    private function createService(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Services;

use {$namespace}\Contracts\\{$name}RepositoryInterface;
use {$namespace}\DTOs\Create{$name}DTO;
use {$namespace}\DTOs\Update{$name}DTO;

class {$name}Service
{
    public function __construct(
        private readonly {$name}RepositoryInterface \$repository
    ) {
    }

    public function all()
    {
        return \$this->repository->all();
    }

    public function find(
        int|string \$id
    ) {
        return \$this->repository->find(\$id);
    }

    public function create(
        Create{$name}DTO \$dto
    ) {
        return \$this->repository->create(\$dto);
    }

    public function update(
        int|string \$id,
        Update{$name}DTO \$dto
    ) {
        return \$this->repository->update(
            \$id,
            \$dto
        );
    }

    public function delete(
        int|string \$id
    ): bool {
        return \$this->repository->delete(\$id);
    }
}

PHP;

        $this->writeFile(
            "{$path}/Services/{$name}Service.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Requests
    |--------------------------------------------------------------------------
    */

    private function createRequests(
        string $path,
        string $namespace,
        string $name
    ): void {
        $store = <<<PHP
<?php

namespace {$namespace}\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

PHP;

        $update = <<<PHP
<?php

namespace {$namespace}\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update{$name}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

PHP;

        $this->writeFile(
            "{$path}/Requests/Store{$name}Request.php",
            $store
        );

        $this->writeFile(
            "{$path}/Requests/Update{$name}Request.php",
            $update
        );
    }

    /*
    |--------------------------------------------------------------------------
    | API Resource
    |--------------------------------------------------------------------------
    */

    private function createResource(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {$name}Resource extends JsonResource
{
    public function toArray(
        Request \$request
    ): array {
        return parent::toArray(\$request);
    }
}

PHP;

        $this->writeFile(
            "{$path}/Resources/{$name}Resource.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Web Controller
    |--------------------------------------------------------------------------
    */

    private function createWebController(
        string $path,
        string $namespace,
        string $name
    ): void {
        $route = $this->routeName($name);
        $variable = $this->variableName($name);

        $content = <<<PHP
<?php

namespace {$namespace}\Controllers\Web;

use {$namespace}\DTOs\Create{$name}DTO;
use {$namespace}\DTOs\Update{$name}DTO;
use {$namespace}\Requests\Store{$name}Request;
use {$namespace}\Requests\Update{$name}Request;
use {$namespace}\Services\\{$name}Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class {$name}Controller
{
    public function __construct(
        private readonly {$name}Service \$service
    ) {
    }

    public function index(): View
    {
        return view(
            '{$route}.index',
            [
                '{$variable}' => \$this->service->all(),
            ]
        );
    }

    public function create(): View
    {
        return view(
            '{$route}.create'
        );
    }

    public function store(
        Store{$name}Request \$request
    ): RedirectResponse {
        \$this->service->create(
            Create{$name}DTO::fromArray(
                \$request->validated()
            )
        );

        return redirect()->route(
            '{$route}.index'
        );
    }

    public function show(
        int|string \$id
    ): View {
        return view(
            '{$route}.show',
            [
                'data' => \$this->service->find(\$id),
            ]
        );
    }

    public function edit(
        int|string \$id
    ): View {
        return view(
            '{$route}.edit',
            [
                'data' => \$this->service->find(\$id),
            ]
        );
    }

    public function update(
        Update{$name}Request \$request,
        int|string \$id
    ): RedirectResponse {
        \$this->service->update(
            \$id,
            Update{$name}DTO::fromArray(
                \$request->validated()
            )
        );

        return redirect()->route(
            '{$route}.index'
        );
    }

    public function destroy(
        int|string \$id
    ): RedirectResponse {
        \$this->service->delete(\$id);

        return redirect()->route(
            '{$route}.index'
        );
    }
}

PHP;

        $this->writeFile(
            "{$path}/Controllers/Web/{$name}Controller.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | API Controller
    |--------------------------------------------------------------------------
    */

    private function createApiController(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Controllers\Api;

use {$namespace}\DTOs\Create{$name}DTO;
use {$namespace}\DTOs\Update{$name}DTO;
use {$namespace}\Requests\Store{$name}Request;
use {$namespace}\Requests\Update{$name}Request;
use {$namespace}\Resources\\{$name}Resource;
use {$namespace}\Services\\{$name}Service;
use Illuminate\Http\JsonResponse;

class {$name}Controller
{
    public function __construct(
        private readonly {$name}Service \$service
    ) {
    }

    public function index()
    {
        return {$name}Resource::collection(
            \$this->service->all()
        );
    }

    public function store(
        Store{$name}Request \$request
    ): JsonResponse {
        \$model = \$this->service->create(
            Create{$name}DTO::fromArray(
                \$request->validated()
            )
        );

        return (new {$name}Resource(\$model))
            ->response()
            ->setStatusCode(201);
    }

    public function show(
        int|string \$id
    ): {$name}Resource {
        return new {$name}Resource(
            \$this->service->find(\$id)
        );
    }

    public function update(
        Update{$name}Request \$request,
        int|string \$id
    ): {$name}Resource {
        return new {$name}Resource(
            \$this->service->update(
                \$id,
                Update{$name}DTO::fromArray(
                    \$request->validated()
                )
            )
        );
    }

    public function destroy(
        int|string \$id
    ): JsonResponse {
        \$this->service->delete(\$id);

        return response()->json([
            'message' => '{$name} deleted successfully.',
        ]);
    }
}

PHP;

        $this->writeFile(
            "{$path}/Controllers/Api/{$name}Controller.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */

    private function createLivewire(
        string $path,
        string $namespace,
        string $name
    ): void {
        $view = $this->viewName($name);
        $variable = $this->variableName($name);

        $content = <<<PHP
<?php

namespace {$namespace}\Livewire;

use {$namespace}\Services\\{$name}Service;
use Livewire\Component;

class {$name}Index extends Component
{
    public function render()
    {
        return view(
            '{$view}::index',
            [
                '{$variable}' => app({$name}Service::class)->all(),
            ]
        );
    }
}

PHP;

        $this->writeFile(
            "{$path}/Livewire/{$name}Index.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    */

    private function createViews(
        string $path,
        string $name
    ): void {
        $variable = $this->variableName($name);

        /*
        |--------------------------------------------------------------------------
        | Index
        |--------------------------------------------------------------------------
        */

        $index = <<<BLADE
<div>
    <h1>{$name}</h1>

    @forelse (\${$variable} as \$item)
        <div>
            {{ \$item->id }}
        </div>
    @empty
        <p>No {$variable} found.</p>
    @endforelse
</div>
BLADE;

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $create = <<<BLADE
@extends('layouts.app')

@section('content')

<div>
    <h1>Create {$name}</h1>

    {{-- Add create form here --}}
</div>

@endsection
BLADE;

        /*
        |--------------------------------------------------------------------------
        | Edit
        |--------------------------------------------------------------------------
        */

        $edit = <<<BLADE
@extends('layouts.app')

@section('content')

<div>
    <h1>Edit {$name}</h1>

    {{-- Add edit form here --}}
</div>

@endsection
BLADE;

        /*
        |--------------------------------------------------------------------------
        | Show
        |--------------------------------------------------------------------------
        */

        $show = <<<BLADE
@extends('layouts.app')

@section('content')

<div>
    <h1>{$name}</h1>

    @isset(\$data)
        {{ \$data->id }}
    @endisset
</div>

@endsection
BLADE;

        $this->writeFile(
            "{$path}/Views/index.blade.php",
            $index
        );

        $this->writeFile(
            "{$path}/Views/create.blade.php",
            $create
        );

        $this->writeFile(
            "{$path}/Views/edit.blade.php",
            $edit
        );

        $this->writeFile(
            "{$path}/Views/show.blade.php",
            $show
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    private function createRoutes(
        string $path,
        string $namespace,
        string $name
    ): void {
        $route = $this->routeName($name);

        /*
        |--------------------------------------------------------------------------
        | Web Routes
        |--------------------------------------------------------------------------
        */

        $web = <<<PHP
<?php

use {$namespace}\Controllers\Web\\{$name}Controller;
use {$namespace}\Livewire\\{$name}Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {

    Route::get(
        '{$route}',
        {$name}Index::class
    )->name('{$route}.index');

    Route::get(
        '{$route}/create',
        [{$name}Controller::class, 'create']
    )->name('{$route}.create');

    Route::post(
        '{$route}',
        [{$name}Controller::class, 'store']
    )->name('{$route}.store');

    Route::get(
        '{$route}/{id}',
        [{$name}Controller::class, 'show']
    )->name('{$route}.show');

    Route::get(
        '{$route}/{id}/edit',
        [{$name}Controller::class, 'edit']
    )->name('{$route}.edit');

    Route::put(
        '{$route}/{id}',
        [{$name}Controller::class, 'update']
    )->name('{$route}.update');

    Route::delete(
        '{$route}/{id}',
        [{$name}Controller::class, 'destroy']
    )->name('{$route}.destroy');
});

PHP;

        /*
        |--------------------------------------------------------------------------
        | API Routes
        |--------------------------------------------------------------------------
        |
        | Important:
        | Do NOT put "api/" here if Laravel already loads this file
        | using the API prefix.
        |
        */

        $api = <<<PHP
<?php

use {$namespace}\Controllers\Api\\{$name}Controller;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {

    Route::apiResource(
        '{$route}',
        {$name}Controller::class
    );

});

PHP;

        $this->writeFile(
            "{$path}/Routes/web.php",
            $web
        );

        $this->writeFile(
            "{$path}/Routes/api.php",
            $api
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Service Provider
    |--------------------------------------------------------------------------
    */

    private function createProvider(
        string $path,
        string $namespace,
        string $name
    ): void {
        $viewName = $this->viewName($name);

        $content = <<<PHP
<?php

namespace {$namespace}\Providers;

use {$namespace}\Contracts\\{$name}RepositoryInterface;
use {$namespace}\Repositories\\{$name}Repository;
use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->app->bind(
            {$name}RepositoryInterface::class,
            {$name}Repository::class
        );
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        \$this->loadViewsFrom(
            __DIR__ . '/../Views',
            '{$viewName}'
        );

        /*
        |--------------------------------------------------------------------------
        | Web Routes
        |--------------------------------------------------------------------------
        */

        \$webRoutes = __DIR__ . '/../Routes/web.php';

        if (file_exists(\$webRoutes)) {
            \$this->loadRoutesFrom(\$webRoutes);
        }

        /*
        |--------------------------------------------------------------------------
        | API Routes
        |--------------------------------------------------------------------------
        */

        \$apiRoutes = __DIR__ . '/../Routes/api.php';

        if (file_exists(\$apiRoutes)) {
            \$this->loadRoutesFrom(\$apiRoutes);
        }

        /*
        |--------------------------------------------------------------------------
        | Migrations
        |--------------------------------------------------------------------------
        */

        \$migrationPath =
            __DIR__ . '/../Database/Migrations';

        if (is_dir(\$migrationPath)) {
            \$this->loadMigrationsFrom(
                \$migrationPath
            );
        }
    }
}

PHP;

        $this->writeFile(
            "{$path}/Providers/{$name}ServiceProvider.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    */

    private function createConfig(
        string $path,
        string $namespace,
        string $name,
        string $scope,
        string $description
    ): void {
        $config = [
            'name' => $name,

            'slug' => $this->routeName($name),

            'scope' => $scope,

            'description' => $description,

            'version' => '1.0.0',

            'namespace' => $namespace,

            'installed' => true,

            'enabled' => true,

            'provider' =>
                "{$namespace}\\Providers\\{$name}ServiceProvider",

            'routes' => [
                'web' =>
                    "{$path}/Routes/web.php",

                'api' =>
                    "{$path}/Routes/api.php",
            ],

            'migration_path' =>
                "{$path}/Database/Migrations",

            'seeder' =>
                "{$namespace}\\Database\\Seeders\\{$name}Seeder",
        ];

        $content =
            "<?php\n\nreturn "
            . var_export($config, true)
            . ";\n";

        $this->writeFile(
            "{$path}/Config/module.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Migration
    |--------------------------------------------------------------------------
    */

    private function createMigration(
        string $path,
        string $name
    ): void {
        $timestamp = now()->format(
            'Y_m_d_His'
        );

        $table = Str::snake(
            Str::pluralStudly($name)
        );

        $filename =
            "{$timestamp}_create_{$table}_table.php";

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            '{$table}',
            function (Blueprint \$table) {
                \$table->id();
                \$table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            '{$table}'
        );
    }
};

PHP;

        $this->writeFile(
            "{$path}/Database/Migrations/{$filename}",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Seeder
    |--------------------------------------------------------------------------
    */

    private function createSeeder(
        string $path,
        string $namespace,
        string $name
    ): void {
        $content = <<<PHP
<?php

namespace {$namespace}\Database\Seeders;

use Illuminate\Database\Seeder;

class {$name}Seeder extends Seeder
{
    public function run(): void
    {
        //
    }
}

PHP;

        $this->writeFile(
            "{$path}/Database/Seeders/{$name}Seeder.php",
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    private function showSuccess(
        string $name,
        string $scope,
        string $description,
        string $namespace,
        string $modulePath
    ): void {
        $this->newLine();

        $this->components->info(
            "Module [{$scope}/{$name}] created successfully."
        );

        $this->newLine();

        $this->table(
            ['Property', 'Value'],
            [
                ['Name', $name],
                ['Scope', $scope],
                ['Description', $description],
                ['Version', '1.0.0'],
                ['Namespace', $namespace],
                ['Location', $modulePath],
                ['Status', 'Enabled'],
                ['Livewire', "{$name}Index"],
            ]
        );

        $this->newLine();

        $this->line(
            '<comment>Next steps:</comment>'
        );

        $this->line(
            '  php artisan migrate'
        );

        $this->line(
            '  php artisan route:list'
        );

        $this->line(
            '  php artisan module:list'
        );

        $this->line(
            '  php artisan module:routes'
        );

        $this->newLine();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function routeName(
        string $name
    ): string {
        return Str::kebab(
            Str::pluralStudly($name)
        );
    }

    private function variableName(
        string $name
    ): string {
        return Str::camel(
            Str::pluralStudly($name)
        );
    }

    private function viewName(
        string $name
    ): string {
        return Str::kebab(
            Str::pluralStudly($name)
        );
    }

    private function writeFile(
        string $path,
        string $content
    ): void {
        if (
            File::exists($path)
            && ! $this->option('force')
        ) {
            $this->warn(
                "Skipped existing file: {$path}"
            );

            return;
        }

        File::ensureDirectoryExists(
            dirname($path)
        );

        File::put(
            $path,
            $content
        );

        $this->line(
            'Created: ' . $path
        );
    }
}
