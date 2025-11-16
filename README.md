# Laravel CRUD Generator with Inertia & Shadcn

[![Latest Version on Packagist](https://img.shields.io/packagist/v/necro304/crud-inertia-shadcn.svg?style=flat-square)](https://packagist.org/packages/necro304/crud-inertia-shadcn)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/necro304/crud-inertia-shadcn/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/necro304/crud-inertia-shadcn/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/necro304/crud-inertia-shadcn/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/necro304/crud-inertia-shadcn/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/necro304/crud-inertia-shadcn.svg?style=flat-square)](https://packagist.org/packages/necro304/crud-inertia-shadcn)

A powerful Laravel package that generates complete, production-ready CRUD modules with a modern tech stack: **Vue 3**, **Inertia.js**, **Shadcn-vue**, and **Tailwind CSS 4**. Generate models, controllers, form requests, API resources, Vue components, and database migrations with a single Artisan command.

## Features

✨ **Complete CRUD Generation**
- **Backend**: Models with relationships, Controllers with filtering/sorting, Form Requests with validation, API Resources
- **Frontend**: Vue 3 components with TypeScript support, Shadcn-vue UI components, Data tables with TanStack Table
- **Database**: Migrations with proper column types and indexes

🎨 **Modern UI Components**
- Built-in Shadcn-vue components (DataTable, Form, Button, Input, Badge, AlertDialog, etc.)
- Responsive design with Tailwind CSS 4
- Dark mode support
- Accessible WCAG-compliant components

🔍 **Advanced Features**
- Soft deletes support
- Model auditing with owen-it/laravel-auditing
- Advanced filtering and sorting with Spatie Query Builder
- Debounced search across multiple fields
- File upload handling with storage integration
- Laravel Wayfinder integration for type-safe routing

📦 **Best Practices**
- PSR-12 code style with Laravel Pint
- PHPStan Level 5 static analysis
- Pest PHP testing framework
- Clean architecture following Laravel conventions
- TypeScript strict mode for frontend code

## Requirements

- **PHP**: ^8.3 | ^8.4
- **Laravel**: ^12.0
- **Node.js**: ^20.0 (for Vue 3 and Vite)
- **Composer**: ^2.0

## Installation

Install the package via Composer:

```bash
composer require necro304/crud-inertia-shadcn
```

The package will automatically register its service provider.

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag="crud-generator-config"
```

This will create a `config/crud-generator.php` file where you can customize:
- Default namespaces
- Directory paths
- Stub templates
- Feature toggles (soft deletes, auditing, etc.)

## Basic Usage

Generate a complete CRUD module with a single command:

```bash
php artisan make:crud Product name:string description:text price:decimal:nullable stock:integer
```

This command generates:

### Backend Files
- ✅ `app/Models/Product.php` - Eloquent model with casts, relationships, soft deletes, and auditing
- ✅ `app/Http/Controllers/ProductController.php` - RESTful controller with index, create, store, show, edit, update, destroy
- ✅ `app/Http/Requests/Product/StoreProductRequest.php` - Validation rules for creating products
- ✅ `app/Http/Requests/Product/UpdateProductRequest.php` - Validation rules for updating products
- ✅ `app/Http/Resources/ProductResource.php` - API resource for JSON transformation
- ✅ `database/migrations/xxxx_xx_xx_create_products_table.php` - Database migration

### Frontend Files (Vue 3 + Inertia.js)
- ✅ `resources/js/pages/Products/Index.vue` - List view with DataTable, filtering, search, and pagination
- ✅ `resources/js/pages/Products/Create.vue` - Creation form
- ✅ `resources/js/pages/Products/Edit.vue` - Edit form with pre-filled data
- ✅ `resources/js/pages/Products/Show.vue` - Detail view
- ✅ `resources/js/pages/Products/components/ProductForm.vue` - Reusable form component

## Command Options

### Field Syntax

Define fields using the format: `name:type[:modifier[:modifier]]`

**Supported Field Types:**
- `string` - VARCHAR(255)
- `text` - TEXT column
- `integer` - INTEGER column
- `decimal` - DECIMAL(8,2) column
- `boolean` - BOOLEAN column
- `date` - DATE column
- `datetime` - DATETIME column
- `timestamp` - TIMESTAMP column
- `json` - JSON column

**Supported Modifiers:**
- `nullable` - Allow NULL values
- `unique` - Add unique constraint
- `index` - Add database index

**Examples:**
```bash
# String field, nullable
php artisan make:crud Post title:string:nullable

# Decimal with nullable
php artisan make:crud Invoice total:decimal:nullable

# Multiple modifiers
php artisan make:crud User email:string:unique:index
```

### Command Flags

| Flag | Description |
|------|-------------|
| `--no-soft-deletes` | Disable soft deletes in the model |
| `--no-auditing` | Disable model auditing |
| `--no-views` | Skip Vue component generation |
| `--table=custom_name` | Specify a custom table name |
| `--force` | Overwrite existing files without confirmation |

**Examples:**
```bash
# Generate without soft deletes
php artisan make:crud Product name:string --no-soft-deletes

# Custom table name
php artisan make:crud UserProfile name:string --table=user_profiles

# Skip frontend generation (API only)
php artisan make:crud Category name:string --no-views

# Overwrite existing files
php artisan make:crud Product name:string --force
```

## Generated Code Patterns

### Model Example

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Product extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'description', 'price', 'stock'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
}
```

### Controller Example

```php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Resources\ProductResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where('name', 'like', "%{$value}%")
                          ->orWhere('description', 'like', "%{$value}%");
                }),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'price', 'created_at'])
            ->defaultSort('-created_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => ProductResource::collection($products),
        ]);
    }

    // ... other CRUD methods
}
```

### Vue Component Example

```vue
<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { DataTable } from '@/components/ui/data-table'
import { useCrudColumns } from '@/composables/useCrudColumns'
import { useDebounceFn } from '@vueuse/core'

const props = defineProps<{
  products: PaginatedResponse<Product>
}>()

const { columns } = useCrudColumns<Product>([
  { key: 'name', label: 'Name', sortable: true },
  { key: 'price', label: 'Price', sortable: true },
  { key: 'stock', label: 'Stock' },
])

const handleSearch = useDebounceFn((value: string) => {
  router.visit(route('products.index'), {
    data: { search: value },
    preserveState: true,
  })
}, 300)
</script>

<template>
  <DataTable
    :columns="columns"
    :data="products.data"
    :pagination="products"
    @search="handleSearch"
  />
</template>
```

## Advanced Usage

### Adding Relationships

After generating a CRUD, you can manually add relationships to the model:

```php
// app/Models/Product.php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}
```

Then update the controller to eager load relationships:

```php
// app/Http/Controllers/ProductController.php
$products = QueryBuilder::for(Product::class)
    ->with(['category', 'reviews']) // Eager load relationships
    ->allowedFilters([...])
    // ...
```

### File Uploads

For models with file upload fields (e.g., images, documents):

```bash
php artisan make:crud Company name:string logo:string:nullable
```

Then update the controller to handle file uploads:

```php
// app/Http/Controllers/CompanyController.php
public function store(StoreCompanyRequest $request)
{
    $data = $request->validated();

    if ($request->hasFile('logo')) {
        $data['logo'] = $request->file('logo')->store('companies/logos', 'public');
    }

    Company::create($data);

    return redirect()->route('companies.index');
}
```

### Custom Validation Messages

Edit the generated Form Request classes to add custom validation messages:

```php
// app/Http/Requests/Product/StoreProductRequest.php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'stock' => ['required', 'integer', 'min:0'],
    ];
}

public function messages(): array
{
    return [
        'name.required' => 'The product name is required.',
        'price.min' => 'The price must be at least 0.',
        'stock.min' => 'Stock cannot be negative.',
    ];
}
```

## Frontend Stack

The generated Vue components use a modern, production-ready stack:

- **Vue 3** - Composition API with `<script setup>` syntax
- **TypeScript** - Strict type checking for all props and interfaces
- **Inertia.js v2** - Server-driven SPA with deferred props, prefetching, and polling
- **Shadcn-vue** - Headless, accessible UI components
- **TanStack Table v8** - Powerful data tables with sorting, filtering, pagination
- **VueUse** - Composables library (e.g., `useDebounceFn` for search)
- **Lucide Icons** - Modern, consistent icon system
- **Tailwind CSS 4** - Utility-first CSS framework
- **Laravel Wayfinder** - Type-safe route helpers for Inertia

## Testing

Run the package tests:

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run PHPStan static analysis
composer analyse

# Fix code style issues
composer format
```

### Writing Tests for Generated Code

Generated CRUD modules should have corresponding tests:

```php
// tests/Feature/ProductControllerTest.php
use App\Models\Product;

it('can list products', function () {
    Product::factory()->count(3)->create();

    $response = $this->get(route('products.index'));

    $response->assertSuccessful();
});

it('can create a product', function () {
    $data = [
        'name' => 'Test Product',
        'price' => 99.99,
        'stock' => 10,
    ];

    $response = $this->post(route('products.store'), $data);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', ['name' => 'Test Product']);
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on recent changes.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [necro304](https://github.com/necro304)
- [All Contributors](../../contributors)

Built with inspiration from the Laravel ecosystem and modern frontend practices.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
