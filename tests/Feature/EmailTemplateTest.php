<?php

use App\Models\Admin;
use App\Models\Marketing\Template;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->user = \App\Models\User::factory()->create();
    $this->admin = Admin::factory()->create([
        'id' => $this->user->id,
    ]);
    actingAs($this->admin, 'admin');
});

it('lists email templates', function () {
    $template = Template::create([
        'user_id' => $this->admin->id,
        'name' => 'Test Email Template',
        'template_id' => 'abc123xyz7890',
        'published' => true,
    ]);

    get(route('admin.email-templates.index'))
        ->assertSuccessful()
        ->assertSee('Test Email Template');
});

it('stores a new blank email template', function () {
    post(route('admin.email-templates.store'), [
        'name' => 'New Blank Template',
        'published' => 1,
    ])
        ->assertRedirect(route('admin.email-templates.index'))
        ->assertSessionHas('success', 'Template created.');

    $created = Template::where('name', 'New Blank Template')->first();
    expect($created)->not->toBeNull()
        ->and($created->published)->toBe(1)
        ->and($created->content)->toBeNull();
});

it('stores a new email template with imported content', function () {
    $importedJson = json_encode(['body' => ['rows' => []]]);

    post(route('admin.email-templates.store'), [
        'name' => 'New Imported Template',
        'published' => 1,
        'content' => $importedJson,
    ])
        ->assertRedirect(route('admin.email-templates.index'))
        ->assertSessionHas('success', 'Template created.');

    $created = Template::where('name', 'New Imported Template')->first();
    expect($created)->not->toBeNull()
        ->and($created->published)->toBe(1)
        ->and($created->content)->toBe($importedJson);
});

it('saves template content via ajax route', function () {
    $template = Template::create([
        'user_id' => $this->admin->id,
        'name' => 'Design Template',
        'template_id' => 'design123xyz',
        'published' => true,
    ]);

    $designJson = json_encode(['body' => ['rows' => ['test']]]);

    post(route('admin.email-templates.save-content', $template), [
        'content' => $designJson,
    ])
        ->assertSuccessful()
        ->assertJson(['saved' => true]);

    $template->refresh();
    expect($template->content)->toBe($designJson);
});

describe('frontend user email template features', function () {
    beforeEach(function () {
        // Authenticate as a normal user for frontend routes
        actingAs($this->user, 'web');
    });

    it('lists admin default templates on frontend page', function () {
        // Create an admin default template
        $adminTemplate = Template::create([
            'user_id' => $this->admin->id,
            'name' => 'Admin Default Spec',
            'template_id' => 'admin-def-spec',
            'published' => true,
        ]);

        get(route('app.template.index'))
            ->assertSuccessful()
            ->assertViewHas('defaultList', function ($list) use ($adminTemplate) {
                return $list->contains('id', $adminTemplate->id);
            });
    });

    it('selects and duplicates admin default template for the user', function () {
        $adminTemplate = Template::create([
            'user_id' => $this->admin->id,
            'name' => 'Admin Default Spec',
            'template_id' => 'admin-def-spec',
            'published' => true,
            'content' => json_encode(['blocks' => [], 'settings' => []]),
        ]);

        post(route('app.template.select'), [
            'id' => 'admin-def-spec',
            'type' => 'admin',
        ])
            ->assertRedirect();

        // Verify copied template in DB
        $copy = Template::where('user_id', $this->user->id)
            ->where('name', 'Copy of Admin Default Spec')
            ->first();

        expect($copy)->not->toBeNull()
            ->and($copy->content)->toBe($adminTemplate->content);
    });

    it('renders the templatical editor view for a database-backed template', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'My DB Template',
            'template_id' => 'user-db-spec',
            'content' => json_encode(['blocks' => [], 'settings' => []]),
        ]);

        get(route('app.template.design', ['id' => 'user-db-spec', 'type' => 'user']))
            ->assertSuccessful()
            ->assertViewIs('marketing.design.templatical')
            ->assertViewHas('template', function ($t) use ($userTemplate) {
                return $t->id === $userTemplate->id;
            });
    });

    it('renders the builderjs editor view for a file-backed template', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'My File Template',
            'template_id' => 'user-file-spec',
            'content' => null,
        ]);

        get(route('app.template.design', ['id' => 'user-file-spec', 'type' => 'user']))
            ->assertSuccessful()
            ->assertViewIs('marketing.design.index');
    });

    it('saves Templatical editor content via the frontend save route', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'My DB Template',
            'template_id' => 'user-db-spec',
            'content' => json_encode(['blocks' => [], 'settings' => []]),
        ]);

        $newJson = json_encode(['blocks' => [['type' => 'paragraph']], 'settings' => []]);

        post(route('app.template.save-content'), [
            'template_id' => 'user-db-spec',
            'content' => $newJson,
        ])
            ->assertSuccessful()
            ->assertJson(['saved' => true]);

        $userTemplate->refresh();
        expect($userTemplate->content)->toBe($newJson);
    });

    it('renames a template via the frontend rename route', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'template_id' => 'user-rename-spec',
        ]);

        post(route('app.template.rename'), [
            'template_id' => 'user-rename-spec',
            'name' => 'Renamed Name',
        ])
            ->assertSuccessful()
            ->assertJson(['success' => true]);

        $userTemplate->refresh();
        expect($userTemplate->name)->toBe('Renamed Name');
    });

    it('duplicates a database template via the frontend duplicate route', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'Source Template',
            'template_id' => 'user-src-spec',
            'content' => json_encode(['blocks' => [], 'settings' => []]),
        ]);

        post(route('app.template.duplicate'), [
            'template_id' => 'user-src-spec',
        ])
            ->assertSuccessful()
            ->assertJson(['success' => true]);

        $duplicate = Template::where('user_id', $this->user->id)
            ->where('name', 'Source Template (Copy)')
            ->first();

        expect($duplicate)->not->toBeNull()
            ->and($duplicate->content)->toBe($userTemplate->content)
            ->and($duplicate->template_id)->not->toBe('user-src-spec');
    });

    it('renders the ellipsis menu button on user saved templates cards', function () {
        $userTemplate = Template::create([
            'user_id' => $this->user->id,
            'name' => 'User Saved Spec',
            'template_id' => 'user-saved-spec',
            'content' => json_encode(['blocks' => [], 'settings' => []]),
        ]);

        $response = get(route('app.template.index'))
            ->assertSuccessful();

        $response->assertSee('hgi-more-horizontal');
    });
});
