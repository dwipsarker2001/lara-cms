# Dynamic Dashboard Notifications Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Transform the dashboard notifications block from a static UI widget into a database-backed dynamic system that updates automatically when public forms are submitted, complete with search and tab-based filtering.

**Architecture:** Create a `Notification` model and table. Populate it with seeder data. Hook into `FormEntry::created` event to automatically save form submissions as notifications. Update `UpdatesListWidget` and `updates-list.blade.php` to bind and filter data using Alpine.js and format times cleanly.

**Tech Stack:** Laravel v13, AlpineJS v3, TailwindCSS v3, Pest v4

## Global Constraints
- Do not introduce external notification dependencies. Use native Eloquent models and hooks.
- Follow existing PHP and coding standards. Maintain docstring integrity.
- Run `vendor/bin/pint --dirty --format agent` to format modified PHP files.

---

### Task 1: Create Database Migration, Notification Model, Factory, and Seeder

**Files:**
- Create: `database/migrations/2026_07_30_000000_create_notifications_table.php`
- Create: `app/Models/Notification.php`
- Create: `database/factories/NotificationFactory.php`
- Create: `database/seeders/NotificationSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Produces: `App\Models\Notification` Eloquent model class with properties: `title`, `sub`, `icon`, `tone`, `period` (accessor), `formatted_time` (accessor)

- [ ] **Step 1: Run artisan command to scaffold the Notification model**

Run command:
```bash
php artisan make:model Notification --migration --factory --no-interaction
```

- [ ] **Step 2: Edit the created migration file**

Open the created migration under `database/migrations/` and write the schema:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sub');
            $table->string('icon')->default('fa-solid fa-comments');
            $table->string('tone')->default('text-text-muted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

- [ ] **Step 3: Edit the Notification model**

Open `app/Models/Notification.php` and write:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'sub', 'icon', 'tone', 'created_at'];

    public function getPeriodAttribute(): string
    {
        $createdAt = $this->created_at;
        if (!$createdAt) {
            return 'Today';
        }

        if ($createdAt->isToday()) {
            return 'Today';
        }

        if ($createdAt->isYesterday()) {
            return 'Yesterday';
        }

        return 'This week';
    }

    public function getFormattedTimeAttribute(): string
    {
        $createdAt = $this->created_at;
        if (!$createdAt) {
            return 'Just now';
        }

        if ($createdAt->isToday()) {
            return $createdAt->format('g:i A');
        }

        if ($createdAt->isYesterday()) {
            return 'Yesterday ' . $createdAt->format('g:i A');
        }

        return $createdAt->format('M d');
    }
}
```

- [ ] **Step 4: Edit the Notification Factory**

Open `database/factories/NotificationFactory.php` and write:
```php
<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'sub' => $this->faker->sentence(6),
            'icon' => 'comments',
            'tone' => 'text-text-muted',
        ];
    }
}
```

- [ ] **Step 5: Create the Notification Seeder**

Write seeder to `database/seeders/NotificationSeeder.php` containing historical and current mock notifications:
```php
<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Notification::truncate();

        $now = Carbon::now();

        $data = [
            // Today
            [
                'title' => 'New Client Added',
                'sub' => 'PT. Alpha Indonesia registered',
                'icon' => 'user-plus',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subMinutes(15),
            ],
            [
                'title' => 'Agent Reassigned',
                'sub' => 'Ticket #2322 moved to Michael Wong',
                'icon' => 'comments',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subMinutes(30),
            ],
            [
                'title' => 'SLA Breach Risk',
                'sub' => "Ticket #2320 'Login issue'",
                'icon' => 'triangle-exclamation',
                'tone' => 'text-red-500',
                'created_at' => $now->copy()->subMinutes(45),
            ],
            [
                'title' => 'Knowledge Base',
                'sub' => "New article published: 'Login Troubleshooting'",
                'icon' => 'book-open',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subMinutes(60),
            ],
            [
                'title' => 'Customer Feedback',
                'sub' => "'Great support response, thanks Sarah!'",
                'icon' => 'star',
                'tone' => 'text-amber-500',
                'created_at' => $now->copy()->subMinutes(60),
            ],

            // Yesterday
            [
                'title' => 'Payment Failed',
                'sub' => 'Invoice #1094 for Acme Corp failed',
                'icon' => 'triangle-exclamation',
                'tone' => 'text-red-500',
                'created_at' => $now->copy()->subDay()->subHours(2),
            ],
            [
                'title' => 'Feedback Form',
                'sub' => "New entry from 'John Doe'",
                'icon' => 'comments',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subDay()->subHours(5),
            ],
            [
                'title' => 'KB Article Edited',
                'sub' => "'Setting up SMTP' updated by Admin',",
                'icon' => 'book-open',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subDay()->subHours(8),
            ],

            // This week
            [
                'title' => 'New Client Added',
                'sub' => 'Stark Industries registered',
                'icon' => 'user-plus',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subDays(2),
            ],
            [
                'title' => 'System Backup',
                'sub' => 'Weekly scheduled backup completed successfully',
                'icon' => 'book-open',
                'tone' => 'text-text-muted',
                'created_at' => $now->copy()->subDays(3),
            ],
            [
                'title' => 'Five-Star Rating',
                'sub' => 'Review by Bruce Wayne on billing speed',
                'icon' => 'star',
                'tone' => 'text-amber-500',
                'created_at' => $now->copy()->subDays(4),
            ],
        ];

        foreach ($data as $item) {
            Notification::create($item);
        }
    }
}
```

- [ ] **Step 6: Register the seeder in DatabaseSeeder**

Modify `database/seeders/DatabaseSeeder.php` to include `NotificationSeeder`:
```diff
        $this->call([
            AdminSeeder::class,
+           NotificationSeeder::class,
        ]);
```

- [ ] **Step 7: Run migrations and seed database**

Run command:
```bash
php artisan migrate:fresh --seed --no-interaction
```

- [ ] **Step 8: Run Pint formatting**

Run command:
```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 2: FormEntry Created Event Listener

**Files:**
- Modify: `app/Models/FormEntry.php`

**Interfaces:**
- Consumes: `App\Models\Notification`

- [ ] **Step 1: Write booted created-hook in FormEntry**

Update `app/Models/FormEntry.php` to automatically create a `Notification` record when a new `FormEntry` is successfully saved:
```diff
class FormEntry extends Model
{
    /** @use HasFactory<FormEntryFactory> */
    use HasFactory;

    protected $fillable = ['form_id', 'data', 'ip_address', 'user_agent', 'status'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => 'integer',
        ];
    }

+   protected static function booted(): void
+   {
+       static::created(function (FormEntry $entry) {
+           $form = $entry->form;
+           $formTitle = $form ? $form->title : 'Form';
+           $formIcon = $form && $form->icon ? $form->icon : 'comments';
+
+           // Remove fa prefixes from icon name if present to keep it simple
+           if (str_starts_with($formIcon, 'fa-solid ')) {
+               $formIcon = substr($formIcon, 9);
+           } elseif (str_starts_with($formIcon, 'fa-')) {
+               $formIcon = substr($formIcon, 3);
+           }
+
+           $sub = '';
+           if (is_array($entry->data)) {
+               $parts = [];
+               foreach (['full_name', 'name', 'email', 'phone'] as $key) {
+                   if (!empty($entry->data[$key])) {
+                       $parts[] = $entry->data[$key];
+                   }
+               }
+               if (empty($parts)) {
+                   foreach ($entry->data as $k => $v) {
+                       if (!empty($v) && is_string($v)) {
+                           $parts[] = "$k: $v";
+                           if (count($parts) >= 2) {
+                               break;
+                           }
+                       }
+                   }
+               }
+               $sub = implode(' - ', $parts);
+           }
+
+           if (empty($sub)) {
+               $sub = 'New submission received';
+           }
+
+           Notification::create([
+               'title' => "New Entry: {$formTitle}",
+               'sub' => $sub,
+               'icon' => $formIcon,
+               'tone' => 'text-text-muted',
+           ]);
+       });
+   }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
```

- [ ] **Step 2: Run Pint formatting**

Run command:
```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 3: Render Notifications and Implement Alpine.js Filter/Search

**Files:**
- Modify: `app/Widgets/UpdatesListWidget.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Modify: `resources/views/admin/widgets/updates-list.blade.php`

**Interfaces:**
- Consumes: `App\Models\Notification`

- [ ] **Step 1: Modify UpdatesListWidget to fetch from database**

Change `app/Widgets/UpdatesListWidget.php` to fetch from the database:
```php
<?php

namespace App\Widgets;

use App\Models\Notification;

class UpdatesListWidget extends Widget
{
    public static function type(): string
    {
        return 'updates_list';
    }

    public static function zone(): string
    {
        return 'list';
    }

    public function label(): string
    {
        return 'Notifications';
    }

    public function render()
    {
        $notifications = Notification::latest()->get();

        $updates = $notifications->map(fn ($n) => (object) [
            'title' => $n->title,
            'sub' => $n->sub,
            'time' => $n->formatted_time,
            'icon' => $n->icon,
            'tone' => $n->tone,
            'period' => $n->period,
        ]);

        return view('admin.widgets.updates-list', compact('updates'));
    }
}
```

- [ ] **Step 2: Add Alpine states and helper to Dashboard**

Update the `dashboard()` method in `resources/views/admin/dashboard.blade.php` (around lines 21-38):
```diff
    function dashboard() {
        return {
            period: 'Today',
            selected: 'Today',
            editing: false,
+           searchQuery: '',
            gridShow: window._dashboardData.gridShow,
            gridOrder: window._dashboardData.gridOrder,
            gridWidgets: window._dashboardData.gridWidgets,
            chartShow: window._dashboardData.chartShow,
            chartWidgets: window._dashboardData.chartWidgets,
            tableShow: window._dashboardData.tableShow,
            tableWidgets: window._dashboardData.tableWidgets,
            listShow: window._dashboardData.listShow,
            listWidgets: window._dashboardData.listWidgets,
            allByZone: window._dashboardData.allByZone,
            tableForms: window._dashboardData.tableForms,
            selectedFormId: null,
            dragIdx: null,
            clickedSlot: null,
            panelOpen: false,
            panelClosing: false,
+           shouldShow(itemPeriod, title, sub) {
+               let matchesPeriod = false;
+               if (this.period === 'Today') {
+                   matchesPeriod = itemPeriod === 'Today';
+               } else if (this.period === 'Yesterday') {
+                   matchesPeriod = itemPeriod === 'Yesterday';
+               } else if (this.period === 'This week') {
+                   matchesPeriod = ['Today', 'Yesterday', 'This week'].includes(itemPeriod);
+               }
+
+               if (!matchesPeriod) return false;
+
+               if (!this.searchQuery) return true;
+               const query = this.searchQuery.toLowerCase();
+               return title.toLowerCase().includes(query) || sub.toLowerCase().includes(query);
+           },
```

- [ ] **Step 3: Update updates-list Blade view for live search and filtering**

Modify `resources/views/admin/widgets/updates-list.blade.php` to wire up Alpine.js variables:
```html
<div class="bg-white rounded-xl ring-1 ring-gray-200 shadow-sm p-4 flex flex-col min-h-0 flex-1"
     x-data="{
        notifications: @json($updates),
        get filteredCount() {
            return this.notifications.filter(item => {
                let matchesPeriod = false;
                if (period === 'Today') {
                    matchesPeriod = item.period === 'Today';
                } else if (period === 'Yesterday') {
                    matchesPeriod = item.period === 'Yesterday';
                } else if (period === 'This week') {
                    matchesPeriod = ['Today', 'Yesterday', 'This week'].includes(item.period);
                }

                if (!matchesPeriod) return false;

                if (!searchQuery) return true;
                const query = searchQuery.toLowerCase();
                return item.title.toLowerCase().includes(query) || item.sub.toLowerCase().includes(query);
            }).length;
        },
        get pendingCount() {
            return this.notifications.filter(item => {
                let matchesPeriod = false;
                if (period === 'Today') {
                    matchesPeriod = item.period === 'Today';
                } else if (period === 'Yesterday') {
                    matchesPeriod = item.period === 'Yesterday';
                } else if (period === 'This week') {
                    matchesPeriod = ['Today', 'Yesterday', 'This week'].includes(item.period);
                }
                if (!matchesPeriod) return false;

                if (searchQuery) {
                    const query = searchQuery.toLowerCase();
                    if (!item.title.toLowerCase().includes(query) && !item.sub.toLowerCase().includes(query)) {
                        return false;
                    }
                }

                return item.tone === 'text-red-500' || item.tone === 'text-amber-500';
            }).length;
        }
     }">
    <div class="flex gap-1 rounded-lg bg-gray-100 p-1">
        @foreach (['Today', 'Yesterday', 'This week'] as $opt)
            <button
                @click="period = '{{ $opt }}'"
                class="flex-1 rounded-md px-2 py-1.5 text-[12px] font-medium transition-colors"
                :class="period === '{{ $opt }}' ? 'bg-white text-text-heading shadow-sm' : 'text-text-muted hover:text-text-heading'"
            >{{ $opt }}</button>
        @endforeach
    </div>
    <div class="relative mt-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-text-muted"><circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" /></svg>
        <input x-model="searchQuery" placeholder="Search notifications" class="w-full rounded-lg border border-content-border bg-white py-2 pl-9 pr-3 text-[13px] text-text-heading placeholder:text-text-muted focus:outline-none focus:ring-2 focus:ring-primary/20">
    </div>
    <div class="mt-3 flex gap-4 text-[12px] text-text-muted">
        <p class="flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 text-text-muted shrink-0">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                <path d="M13.73 21a2 2 0 0 1-3.46 0" />
            </svg>
            <span><span class="font-medium text-text-heading" x-text="filteredCount">8</span> new notifications <span x-text="period === 'This week' ? 'this week' : period.toLowerCase()">today</span></span>
        </p>
        <p><span class="font-medium text-text-heading" x-text="pendingCount">3</span> pending reviews</p>
    </div>
    <ul class="mt-2 flex-1 divide-y divide-content-border" x-show="filteredCount > 0">
        @foreach ($updates as $u)
            <li class="flex items-start gap-3 py-3"
                x-show="shouldShow('{{ $u->period }}', '{{ addslashes($u->title) }}', '{{ addslashes($u->sub) }}')">
                <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-gray-100">
                    @php $iconName = $u->icon; @endphp
                    @if ($iconName === 'user-plus')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="8.5" cy="7" r="4" /><line x1="20" y1="8" x2="20" y2="14" /><line x1="23" y1="11" x2="17" y2="11" /></svg>
                    @elseif ($iconName === 'comments')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" /></svg>
                    @elseif ($iconName === 'triangle-exclamation')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>
                    @elseif ($iconName === 'book-open')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z" /><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" /></svg>
                    @elseif ($iconName === 'star')
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-3.5 {{ $u->tone }}"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" /></svg>
                    @else
                        <i class="fa-solid fa-{{ $u->icon }} {{ $u->tone }} text-[14px]"></i>
                    @endif
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate text-[13px] font-medium text-text-heading">{{ $u->title }}</span>
                        <span class="shrink-0 text-[11px] text-text-muted">{{ $u->time }}</span>
                    </div>
                    <p class="mt-0.5 truncate text-[12px] text-text-muted">{{ $u->sub }}</p>
                </div>
            </li>
        @endforeach
    </ul>
    <div x-show="filteredCount === 0" class="mt-8 text-center py-6">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="size-8 mx-auto text-text-muted/60 mb-2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></svg>
        <p class="text-[13px] text-text-muted">No notifications found</p>
    </div>
</div>
```

- [ ] **Step 4: Run Pint formatting**

Run command:
```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 4: Testing & Verification

**Files:**
- Create: `tests/Feature/DashboardNotificationsTest.php`

**Interfaces:**
- Consumes: Form, FormEntry, Notification

- [ ] **Step 1: Write Feature test file for notifications**

Create `tests/Feature/DashboardNotificationsTest.php` with the following content:
```php
<?php

use App\Models\Admin;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    actingAs($this->admin, 'admin');
});

it('automatically creates a notification on form entry submission', function () {
    $form = Form::factory()->create([
        'title' => 'Contact Inquiry',
        'icon' => 'fa-solid fa-comments',
    ]);

    post(route('public.forms.submit', $form->id), [
        'full_name' => 'Dwip Sarker',
        'email' => 'dwip@example.com',
        'message' => 'Hello there',
    ]);

    $notification = Notification::where('title', 'New Entry: Contact Inquiry')->first();
    expect($notification)->not->toBeNull();
    expect($notification->sub)->toContain('Dwip Sarker');
    expect($notification->sub)->toContain('dwip@example.com');
});

it('renders dashboard with dynamic notifications database content', function () {
    Notification::create([
        'title' => 'Custom SLA Alert',
        'sub' => 'Severity 1 issue flagged',
        'icon' => 'triangle-exclamation',
        'tone' => 'text-red-500',
    ]);

    get(route('admin.dashboard'))
        ->assertStatus(200)
        ->assertSee('Custom SLA Alert')
        ->assertSee('Severity 1 issue flagged');
});
```

- [ ] **Step 2: Run Pest tests**

Run command:
```bash
php artisan test tests/Feature/DashboardNotificationsTest.php --compact
```
Expected: PASS

- [ ] **Step 3: Run all project tests**

Run command:
```bash
php artisan test --compact
```
Expected: PASS
