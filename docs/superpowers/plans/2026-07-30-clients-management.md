# Clients Management Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create a clean Client management CRUD module in the admin panel, allowing admins to add, edit, and delete client information (Name, Email, Phone, Company, Notes) without needing a password.

**Architecture:** Create a `Client` model and database migration. Register `ClientController` routes under `admin` prefix. Update the admin layout sidebar to include a "Clients" link. Build standard Blade views using Tailwind and AlpineJS for table listing, creation, and editing.

**Tech Stack:** Laravel v13, AlpineJS v3, TailwindCSS v3, Pest v4

## Global Constraints
- Follow existing PHP and coding standards. Maintain docstring integrity.
- Run `vendor/bin/pint --dirty --format agent` to format modified PHP files.

---

### Task 1: Scaffolding Client Model, Migration, Factory, and Seeder

**Files:**
- Create: `app/Models/Client.php`
- Create: `database/migrations/2026_07_30_100001_create_clients_table.php`
- Create: `database/factories/ClientFactory.php`
- Create: `database/seeders/ClientSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Produces: `App\Models\Client` Eloquent model class with fillable properties: `name`, `email`, `phone`, `company`, `notes`.

- [ ] **Step 1: Scaffolding the Client Model and basic files**
Run:
```bash
php artisan make:model Client --migration --factory --no-interaction
```

- [ ] **Step 2: Update the migration file**
Open the generated migration file under `database/migrations/` and write:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
```

- [ ] **Step 3: Update the Client Model**
Open `app/Models/Client.php` and write:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'company', 'notes'];
}
```

- [ ] **Step 4: Update the Client Factory**
Open `database/factories/ClientFactory.php` and write:
```php
<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'company' => $this->faker->company(),
            'notes' => $this->faker->paragraph(),
        ];
    }
}
```

- [ ] **Step 5: Create ClientSeeder**
Create `database/seeders/ClientSeeder.php` and write:
```php
<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        Client::truncate();

        Client::create([
            'name' => 'John Doe',
            'email' => 'john@acme.com',
            'phone' => '+1234567890',
            'company' => 'Acme Corp',
            'notes' => 'Key customer for enterprise software.',
        ]);

        Client::create([
            'name' => 'Jane Smith',
            'email' => 'jane@stark.com',
            'phone' => '+0987654321',
            'company' => 'Stark Industries',
            'notes' => 'Requires weekly follow-ups on progress.',
        ]);

        Client::factory()->count(10)->create();
    }
}
```

- [ ] **Step 6: Register ClientSeeder in DatabaseSeeder**
Modify `database/seeders/DatabaseSeeder.php` to run `ClientSeeder`:
```php
        $this->call([
            AdminSeeder::class,
            NotificationSeeder::class,
            ClientSeeder::class,
        ]);
```

- [ ] **Step 7: Run migration and seeder**
Run command:
```bash
DB_HOST=127.0.0.1 php artisan migrate:fresh --seed --no-interaction
```

- [ ] **Step 8: Format files with Pint**
Run command:
```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 2: Create Admin ClientController and Register Routes

**Files:**
- Create: `app/Http/Controllers/Admin/ClientController.php`
- Modify: `routes/admin.php`

**Interfaces:**
- Produces: `App\Http\Controllers\Admin\ClientController` supporting CRUD methods: `index`, `create`, `store`, `edit`, `update`, `destroy`.

- [ ] **Step 1: Scaffold ClientController**
Run:
```bash
php artisan make:controller Admin/ClientController --no-interaction
```

- [ ] **Step 2: Implement ClientController methods**
Modify `app/Http/Controllers/Admin/ClientController.php` to write:
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::orderBy('name')->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Client::create($data);

        return redirect()->route('admin.clients.index')->with('success', 'Client created successfully.');
    }

    public function edit(Client $client): View
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($data);

        return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
    }
}
```

- [ ] **Step 3: Register route in routes/admin.php**
Add the new client resource route in `routes/admin.php` right next to the `users` route:
```php
    Route::resource('clients', ClientController::class)->except(['show']);
```
Import `ClientController` at the top of `routes/admin.php`:
```php
use App\Http\Controllers\Admin\ClientController;
```

- [ ] **Step 4: Format code with Pint**
Run:
```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 3: Build Client Blade Views and Register Sidebar Link

**Files:**
- Create: `resources/views/admin/clients/index.blade.php`
- Create: `resources/views/admin/clients/create.blade.php`
- Create: `resources/views/admin/clients/edit.blade.php`
- Modify: `resources/views/admin/layout.blade.php`

- [ ] **Step 1: Create clients/index.blade.php**
Write `resources/views/admin/clients/index.blade.php`:
```html
@extends('admin.layout')

@section('title', 'Clients')

@section('content')
<div x-data="clientPage()">
    <div class="max-w-5xl mx-auto">
        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-6 shrink-0 text-text-muted">
                    <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                    <circle cx="8.5" cy="7" r="4" />
                    <line x1="20" y1="8" x2="20" y2="14" />
                    <line x1="23" y1="11" x2="17" y2="11" />
                </svg>
                Clients
            </h1>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.clients.create') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Client
                </a>
            </div>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-2">
            <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 px-2 pb-2.5">
                <span class="flex items-center gap-2 text-[14px] font-medium text-text-heading whitespace-nowrap shrink-0">
                    All Clients
                </span>
                <div class="flex items-center gap-2 flex-nowrap shrink-0">
                    <div class="relative shrink-0">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-text-muted">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                        <input
                            id="client-search"
                            type="text"
                            x-model="search"
                            placeholder="Search clients..."
                            class="h-8 w-44 sm:w-56 rounded-lg border border-content-border bg-content-bg pl-8 pr-3 text-[12px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/10 shadow-sm"
                        >
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm text-text-primary">
                    <thead>
                        <tr class="border-b border-content-border bg-gray-50 text-[11px] font-semibold uppercase tracking-wider text-text-muted">
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Phone</th>
                            <th class="px-6 py-3">Company</th>
                            <th class="px-6 py-3">Notes</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-content-border bg-content-bg">
                        @if ($clients->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-text-muted">
                                    No clients yet. <a href="{{ route('admin.clients.create') }}" class="text-primary hover:underline">Add your first client</a>.
                                </td>
                            </tr>
                        @else
                            @foreach ($clients as $client)
                                <tr x-show="matchesSearch({{ Js::from($client->name) }}, {{ Js::from($client->email) }}, {{ Js::from($client->company) }})">
                                    <td class="px-6 py-4 font-medium text-text-heading">{{ $client->name }}</td>
                                    <td class="px-6 py-4">{{ $client->email ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $client->phone ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $client->company ?? '-' }}</td>
                                    <td class="px-6 py-4 truncate max-w-xs">{{ $client->notes ?? '-' }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.clients.edit', $client) }}" class="text-xs font-semibold text-primary hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function clientPage() {
        return {
            search: '',
            matchesSearch(name, email, company) {
                const q = this.search.toLowerCase().trim();
                if (!q) return true;
                return (name && name.toLowerCase().includes(q)) || 
                       (email && email.toLowerCase().includes(q)) || 
                       (company && company.toLowerCase().includes(q));
            }
        }
    }
</script>
@endpush
@endsection
```

- [ ] **Step 2: Create clients/create.blade.php**
Write `resources/views/admin/clients/create.blade.php`:
```html
@extends('admin.layout')

@section('title', 'Add Client')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <form method="POST" action="{{ route('admin.clients.store') }}">
        @csrf

        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                Add Client
            </h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.clients.index') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200">
                    Back
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    Create Client
                </button>
            </div>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Client Details</div>
            <p class="px-[18px] pb-3 text-sm text-text-muted">Enter the contact information for the new client.</p>
            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-4">
                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="name">
                            Name <span class="text-red-600">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Enter client name"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter client email"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="phone">Phone Number</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="Enter phone number"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="company">Company</label>
                        <input id="company" type="text" name="company" value="{{ old('company') }}" placeholder="Enter company name"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('company') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Enter notes or additional details"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
```

- [ ] **Step 3: Create clients/edit.blade.php**
Write `resources/views/admin/clients/edit.blade.php`:
```html
@extends('admin.layout')

@section('title', 'Edit Client')

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <form method="POST" action="{{ route('admin.clients.update', $client) }}">
        @csrf
        @method('PUT')

        <header class="relative flex flex-wrap items-center justify-between gap-4 px-2 sm:px-0 py-6 md:py-8">
            <h1 class="text-[25px] leading-[1.25] font-medium flex items-center gap-2.5 text-text-heading">
                Edit Client: {{ $client->name }}
            </h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.clients.index') }}"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-white hover:bg-gray-50 text-text-heading shadow-sm border border-gray-200">
                    Back
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap shrink-0 font-medium cursor-pointer no-underline rounded-lg transition-colors h-10 text-sm leading-tight px-4 bg-primary hover:opacity-90 text-white shadow-sm">
                    Save Changes
                </button>
            </div>
        </header>

        <div class="bg-panel-bg rounded-2xl mb-8 p-[7px]">
            <div class="px-[18px] pt-3 pb-1 text-sm font-medium text-text-heading">Client Details</div>
            <p class="px-[18px] pb-3 text-sm text-text-muted">Modify the contact information for this client.</p>
            <div class="px-1.5 pb-2">
                <div class="bg-content-bg rounded-xl ring-1 ring-content-border shadow-sm px-4 sm:px-[18px] py-5 space-y-4">
                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="name">
                            Name <span class="text-red-600">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}" required placeholder="Enter client name"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $client->email) }}" placeholder="Enter client email"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('email') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="phone">Phone Number</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="Enter phone number"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('phone') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="company">Company</label>
                        <input id="company" type="text" name="company" value="{{ old('company', $client->company) }}" placeholder="Enter company name"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 h-10 leading-[1.375rem] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        @error('company') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="min-w-0 flex flex-col gap-2">
                        <label class="text-sm font-medium text-text-primary" for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Enter notes or additional details"
                            class="w-full block bg-content-bg border border-content-border text-text-primary placeholder:text-text-muted shadow-sm text-base rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ old('notes', $client->notes) }}</textarea>
                        @error('notes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
```

- [ ] **Step 4: Register Clients in the sidebar**
Open `resources/views/admin/layout.blade.php` and find the sidebar link for Users (lines 328-340). Right after it, add the link for Clients:
```html
                        <li>
                            <a href="{{ route('admin.clients.index') }}"
                                class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm no-underline transition-colors @if(request()->routeIs('admin.clients.*')) text-text-heading bg-gray-200 font-semibold @else text-text-primary hover:bg-gray-100 hover:text-text-heading font-medium @endif"
                            >
                                <span class="flex w-4 shrink-0 items-center justify-center">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-4">
                                        <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                        <circle cx="8.5" cy="7" r="4" />
                                        <line x1="20" y1="8" x2="20" y2="14" />
                                        <line x1="23" y1="11" x2="17" y2="11" />
                                    </svg>
                                </span>
                                Clients
                            </a>
                        </li>
```

---

### Task 4: Testing & Verification

**Files:**
- Create: `tests/Feature/ClientManagementTest.php`

- [ ] **Step 1: Write Feature test for clients**
Create `tests/Feature/ClientManagementTest.php` and write the tests.

- [ ] **Step 2: Run Client tests**
Run:
```bash
php artisan test tests/Feature/ClientManagementTest.php --compact
```
Expected: PASS

- [ ] **Step 3: Run full suite**
Run:
```bash
php artisan test --compact
```
Expected: PASS
