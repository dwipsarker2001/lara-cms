<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Asset;
use App\Services\AiAgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiAgentTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'is_active' => true,
        ]);
    }

    public function test_guest_cannot_access_ai_chat(): void
    {
        $response = $this->post(route('admin.ai.chat'), [
            'prompt' => 'Hello',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_call_ai_chat(): void
    {
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'thought' => 'Adding a hero section as requested',
                                'message' => 'I have added a modern Hero section.',
                                'actions' => [
                                    [
                                        'action' => 'add_section',
                                        'name' => 'heroBanner',
                                        'data' => [
                                            'headline' => 'Welcome to Lara-CMS',
                                        ],
                                        'position' => 0,
                                    ],
                                ],
                                'suggestions' => [
                                    'Add Testimonials',
                                ],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai.chat'), [
            'prompt' => 'Add a hero section',
            'sections' => [],
            'schemas' => ['heroBanner' => []],
            'blockList' => [['name' => 'heroBanner', 'label' => 'Hero Banner']],
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'I have added a modern Hero section.',
        ]);
        $response->assertJsonCount(1, 'actions');
        $this->assertEquals('add_section', $response->json('actions.0.action'));
    }

    public function test_admin_can_retrieve_available_assets_for_ai(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('assets/test-image.jpg', 'image_data');

        Asset::create([
            'name' => 'test-image.jpg',
            'path' => 'assets/test-image.jpg',
            'size' => 1024,
            'mime' => 'image/jpeg',
            'is_directory' => false,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->getJson(route('admin.ai.assets'));

        $response->assertOk();
        $response->assertJsonStructure([
            'assets' => [
                '*' => ['id', 'name', 'url', 'path', 'mime', 'size'],
            ],
            'count',
        ]);
        $this->assertGreaterThanOrEqual(1, $response->json('count'));
    }

    public function test_ai_agent_service_handles_markdown_wrapped_json(): void
    {
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => "```json\n".json_encode([
                                'thought' => 'Testing markdown parser',
                                'message' => 'Parsed correctly!',
                                'actions' => [],
                                'suggestions' => [],
                            ])."\n```",
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(AiAgentService::class);
        $result = $service->chat([
            ['role' => 'user', 'content' => 'Hello'],
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Parsed correctly!', $result['message']);
    }

    public function test_ai_agent_service_handles_unescaped_newlines_and_control_chars(): void
    {
        $rawWithNewlines = "{\n\"thought\": \"Testing raw newlines\",\n\"message\": \"Paragraph 1\n\nParagraph 2\",\n\"actions\": [\n{\n\"action\": \"update_field\",\n\"section_index\": 0,\n\"field_path\": \"mapImage\",\n\"value\": \"https://maps.google.com\"\n}\n],\n\"suggestions\": [\"Next\"]\n}";

        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => $rawWithNewlines,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = app(AiAgentService::class);
        $result = $service->chat([
            ['role' => 'user', 'content' => 'Update map'],
        ]);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['actions']);
        $this->assertEquals('update_field', $result['actions'][0]['action']);
        $this->assertEquals('mapImage', $result['actions'][0]['field_path']);
    }

    public function test_ai_chat_returns_complex_multi_actions_and_image_selection(): void
    {
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'thought' => 'Adding testimonials and setting hero image',
                                'message' => 'Added testimonials and updated hero image.',
                                'actions' => [
                                    [
                                        'action' => 'set_image',
                                        'section_index' => 0,
                                        'field_path' => 'backgroundImage',
                                        'image_url' => '/storage/assets/hero.jpg',
                                    ],
                                    [
                                        'action' => 'update_field',
                                        'section_index' => 0,
                                        'field_path' => 'headline',
                                        'value' => 'Ultimate Adventure Awaits',
                                    ],
                                    [
                                        'action' => 'add_section',
                                        'name' => 'clientTestimonials',
                                        'data' => [
                                            'headline' => 'Customer Stories',
                                        ],
                                        'position' => 1,
                                    ],
                                    [
                                        'action' => 'navigate_to_field',
                                        'section_index' => 1,
                                        'field_path' => 'headline',
                                    ],
                                ],
                                'suggestions' => [
                                    'Save Page',
                                ],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai.chat'), [
            'prompt' => 'Update hero and add testimonials',
            'sections' => [
                [
                    '_key' => 'sec_1',
                    'name' => 'heroBanner',
                    'enabled' => true,
                    'data' => ['headline' => 'Old Title', 'backgroundImage' => ''],
                ],
            ],
            'schemas' => [
                'heroBanner' => [],
                'clientTestimonials' => [],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonCount(4, 'actions');
        $this->assertEquals('set_image', $response->json('actions.0.action'));
        $this->assertEquals('update_field', $response->json('actions.1.action'));
        $this->assertEquals('add_section', $response->json('actions.2.action'));
        $this->assertEquals('navigate_to_field', $response->json('actions.3.action'));
    }

    public function test_writing_content_intent_modifies_fields_without_adding_sections(): void
    {
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'thought' => 'User asked to write engaging copy for the hero banner. Updating existing fields in place.',
                                'message' => 'I have polished the headline and description for your hero section.',
                                'actions' => [
                                    [
                                        'action' => 'update_field',
                                        'section_index' => 0,
                                        'field_path' => 'headline',
                                        'value' => 'Explore the World with Confidence',
                                    ],
                                    [
                                        'action' => 'update_field',
                                        'section_index' => 0,
                                        'field_path' => 'description',
                                        'value' => 'Handpicked luxury stays and curated travel itineraries crafted for discerning travelers.',
                                    ],
                                    [
                                        'action' => 'navigate_to_field',
                                        'section_index' => 0,
                                        'field_path' => 'headline',
                                    ],
                                ],
                                'suggestions' => [
                                    'Change background image',
                                ],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai.chat'), [
            'prompt' => 'Write better content and headline for this hero block',
            'activeSectionIndex' => 0,
            'activeSectionName' => 'heroBanner',
            'sections' => [
                [
                    '_key' => 'sec_1',
                    'name' => 'heroBanner',
                    'enabled' => true,
                    'data' => ['headline' => 'Welcome', 'description' => 'We do travel.'],
                ],
            ],
        ]);

        $response->assertOk();
        $response->assertJsonCount(3, 'actions');

        // Verify no add_section actions were generated
        $actionTypes = collect($response->json('actions'))->pluck('action')->all();
        $this->assertNotContains('add_section', $actionTypes);
        $this->assertContains('update_field', $actionTypes);
    }

    public function test_admin_can_search_images_for_ai_agent(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('assets/nature_forest.jpg', 'fake-image-content');
        Storage::disk('public')->put('assets/beach_sunset.png', 'fake-image-content');

        Asset::create([
            'name' => 'Nature Forest',
            'path' => 'assets/nature_forest.jpg',
            'mime' => 'image/jpeg',
            'size' => 1024,
            'is_directory' => false,
        ]);

        Asset::create([
            'name' => 'Beach Sunset',
            'path' => 'assets/beach_sunset.png',
            'mime' => 'image/png',
            'size' => 2048,
            'is_directory' => false,
        ]);

        // Search with query 'forest'
        $response = $this->actingAs($this->admin, 'admin')->getJson(route('admin.ai.search-images', ['q' => 'forest']));
        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'images');
        $this->assertEquals('Nature Forest', $response->json('images.0.name'));

        // Retrieve all via images alias
        $allResponse = $this->actingAs($this->admin, 'admin')->getJson(route('admin.ai.images'));
        $allResponse->assertOk();
        $allResponse->assertJsonPath('success', true);
        $allResponse->assertJsonCount(2, 'images');
    }

    public function test_admin_can_send_ai_chat_prompt_and_receive_json(): void
    {
        Http::fake([
            'https://api.deepseek.com/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'thought' => 'Prompt test',
                                'message' => 'Processed response',
                                'actions' => [],
                                'suggestions' => [],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(route('admin.ai.chat'), [
            'prompt' => 'Tell me about the site',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Processed response');
    }
}
