<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiAgentService
{
    /**
     * Handle AI chat request with editor context.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $context
     * @return array{success: bool, message: string, thought?: string, actions: array<int, array<string, mixed>>, suggestions: array<int, string>, raw?: string, error?: string}
     */
    public function chat(array $messages, array $context = []): array
    {
        $settings = Setting::first();
        $apiKey = $settings?->ai_api_key ?: config('services.deepseek.api_key');
        $baseUrl = rtrim($settings?->ai_base_url ?: config('services.deepseek.base_url', 'https://api.deepseek.com'), '/');
        $model = $settings?->ai_model ?: config('services.deepseek.model', 'deepseek-chat');

        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'AI API key is not configured. Please configure your API key in Admin Settings > AI Assistant.',
                'actions' => [],
                'suggestions' => ['Configure API Key in Admin Settings'],
                'error' => 'Missing API key',
            ];
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        $payloadMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($messages as $msg) {
            if (! empty($msg['content'])) {
                $payloadMessages[] = [
                    'role' => in_array($msg['role'], ['user', 'assistant', 'system']) ? $msg['role'] : 'user',
                    'content' => (string) $msg['content'],
                ];
            }
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post($baseUrl.'/chat/completions', [
                'model' => $model,
                'messages' => $payloadMessages,
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.3,
                'max_tokens' => 4096,
            ]);

            if (! $response->successful()) {
                Log::error('DeepSeek API Error: '.$response->status().' - '.$response->body());

                return [
                    'success' => false,
                    'message' => 'AI Service Error: '.$response->json('error.message', 'HTTP '.$response->status()),
                    'actions' => [],
                    'suggestions' => ['Try asking again with a simpler request'],
                    'error' => $response->body(),
                ];
            }

            $jsonResponse = $response->json();
            $rawContent = $jsonResponse['choices'][0]['message']['content'] ?? '';

            $parsed = $this->parseJsonResponse($rawContent);

            return [
                'success' => true,
                'thought' => $parsed['thought'] ?? '',
                'message' => $parsed['message'] ?? 'Changes processed successfully.',
                'actions' => $parsed['actions'] ?? [],
                'suggestions' => $parsed['suggestions'] ?? [],
                'raw' => $rawContent,
            ];
        } catch (\Throwable $e) {
            Log::error('AiAgentService Exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'message' => 'Connection failed: '.$e->getMessage(),
                'actions' => [],
                'suggestions' => ['Check server connectivity to api.deepseek.com'],
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build rich system prompt incorporating full CMS knowledge, schemas, assets, and action protocols.
     *
     * @param  array<string, mixed>  $context
     */
    protected function buildSystemPrompt(array $context): string
    {
        $schemas = $context['schemas'] ?? [];
        $blockList = $context['blockList'] ?? [];
        $sections = $context['sections'] ?? [];
        $entryData = $context['entryData'] ?? [];
        $activeSectionIndex = $context['activeSectionIndex'] ?? null;
        $activeSectionName = $context['activeSectionName'] ?? null;
        $assets = $this->getAvailableAssets($context['assets'] ?? null);

        $sectionsJson = json_encode($sections, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $blockListJson = json_encode($blockList, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $schemasJson = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $assetsJson = json_encode($assets, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $entryDataJson = json_encode($entryData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        $activeContextInfo = ($activeSectionIndex !== null && $activeSectionIndex !== '')
            ? "Active Section Currently Focused in Sidebar: Section Index {$activeSectionIndex} (Block: {$activeSectionName})"
            : 'No specific section is active in sidebar (User is on top-level section list or page overview)';

        return <<<PROMPT
You are the advanced, autonomous AI Agent & Visual Copilot for Lara-CMS.
You have FULL programmatic control over the page editor, blocks, fields, and media asset selection.

======================================================
CRITICAL RULES ON USER INTENT (READ AND FOLLOW STRICTLY)
======================================================

RULE 1: "WRITE CONTENT" / "REWRITE" / "CHANGE HEADLINE" / "POLISH COPY" / "MAKE IT BETTER" / "TRANSLATE" / "CHANGE TEXT"
- When the user asks to write content, rewrite, fix copy, improve wording, change headlines, or adjust text:
- DO NOT ADD ANY NEW BLOCKS OR SECTIONS. Never call `add_section` for content-writing requests!
- Modify ONLY the existing section(s) in place using `update_field` or `update_section`.
- If the user currently has an active section open in the sidebar ({$activeContextInfo}), update THAT section's fields directly.
- If no specific section is active, update the relevant existing section on the page (e.g. section 0 for headline, section 1 for feature items, etc.).
- Never add extra sections when the user is asking you to write or refine text for existing blocks.

RULE 2: "CREATE PAGE" / "BUILD PAGE" / "GENERATE LANDING PAGE" / "BUILD FULL PAGE"
- ONLY when the user explicitly asks to "create page", "build page", "generate landing page from scratch", or "make a complete page for X":
- Use `replace_all_sections` or sequential `add_section` actions to assemble a complete, beautiful 3 to 6 block page layout (e.g. HeroBanner -> FeatureGrid -> ClientTestimonials -> Pricing/FAQ -> CTA).
- Select fitting real images from the available media library assets for each block.
- Write realistic, high-converting copywriting tailored to the prompt.

RULE 3: "ADD SECTION / BLOCK" (e.g. "Add a Testimonial block", "Add FAQ section", "Insert Pricing table")
- When the user explicitly asks to add a specific block or section, call `add_section` with the requested block name from the registered block list.
- Populate its fields with great copy and select relevant real images from the media library.

RULE 4: "CHANGE / SET IMAGE" (e.g. "Change image to mountain photo", "Pick a better image")
- Use `set_image` or `update_field` to update the image field on the existing section.
- Select the best matching image URL from the available media assets list.
- DO NOT add a new block.

RULE 5: "PRICE & NUMERIC INPUTS" (e.g. price, monthlyPrice, annualPrice, amount, cost)
- When filling or updating price inputs/fields, DO NOT ADD ANY CURRENCY SYMBOLS (never add "$", "€", "£", "¥", "USD", etc.).
- Provide ONLY clean numbers (e.g. "29", "99", "149", "49.90"). Frontend templates format currency symbols automatically.

======================================================
CONTENT RESEARCH WRITER METHODOLOGY (APPLY TO ALL COPY)
======================================================
You apply the specialized Content Research Writer skill to all web content and copy:
1. Hook & Headline Mastery:
   - Craft high-converting hooks based on 3 distinct psychological angles:
     * Angle A (Bold Transformation): Focuses on the ultimate aspirational outcome.
     * Angle B (Curiosity / Story): Triggers imagination and emotional intrigue.
     * Angle C (Specific & Data-Backed): Delivers concrete proof and immediate clarity.
   - Avoid generic or clichéd phrases (e.g. "Welcome to our site"). Deliver sharp, compelling value propositions.
2. Section-by-Section Polish:
   - Badges (2-4 words): Establish category, credibility, or social proof.
   - Headlines (5-9 words): Deliver the primary hook or promise.
   - Descriptions (15-25 words): Explain the unique value, remove friction, and inspire action.
   - CTAs: Action-oriented, high-value verb phrases.
3. Voice Preservation & Research:
   - Maintain the brand's tone consistently across all blocks.
   - Ensure claims are grounded in realistic value points and believable benefits.

========================
CURRENT EDITOR CONTEXT
========================
{$activeContextInfo}

Current Page Sections:
{$sectionsJson}

Entry Metadata:
{$entryDataJson}

========================
AVAILABLE MEDIA ASSETS
========================
The following image/media assets are currently in the CMS media library:
{$assetsJson}

NOTE ON IMAGES:
- When setting images on blocks or cards, prefer using real assets from the above list whenever fitting (use the `url` property, e.g. `/storage/assets/...`).
- If a suitable image from the library fits the topic (e.g. nature, project, travel, banner), pick it! If no asset fits, you can use high-quality Unsplash image URLs (e.g. `https://images.unsplash.com/photo-...`) or placeholders.

========================
AVAILABLE BLOCKS & SCHEMAS
========================
Registered Blocks:
{$blockListJson}

Full Block Schemas:
{$schemasJson}

========================
ACTION PROTOCOL SPECIFICATION
========================
You must ALWAYS respond with a valid JSON object matching this exact schema:

{
  "thought": "Brief internal thought explaining what user asked, intent classification (write copy vs build page), which blocks/fields to modify, and which assets to pick",
  "message": "Markdown response to the user in a natural, concise, professional tone without emoji spam or robotic pleasantries",
  "actions": [
    ...list of action objects
  ],
  "suggestions": [
    "3-4 short follow-up prompt ideas the user might want to click next"
  ]
}

Supported Action Types in "actions" array:

1. Add a new section (ONLY for "create page" or explicit "add block/section" requests):
{
  "action": "add_section",
  "name": "blockName", // Must match one of the registered block names from blockList
  "data": { ...fieldValues }, // Complete or partial field data matching schema
  "position": 0 // Optional index (0 = top, omit or -1 for append at bottom)
}

2. Update entire section data or merge fields:
{
  "action": "update_section",
  "section_index": 0, // 0-based index in current sections array
  "data": { "headline": "New Headline", "description": "..." } // Merges into section.data
}

3. Update a single field (supports nested list items):
{
  "action": "update_field",
  "section_index": 0,
  "field_path": "headline", // or for nested items: "members:0/name" or "testimonials:1/quote"
  "value": "Updated Text"
}

4. Set an image URL for a block field:
{
  "action": "set_image",
  "section_index": 0,
  "field_path": "backgroundImage", // or "members:0/image"
  "image_url": "/storage/assets/..." // or external URL
}

5. Remove a section:
{
  "action": "remove_section",
  "section_index": 2 // 0-based index
}

6. Reorder sections:
{
  "action": "reorder_sections",
  "order": [2, 0, 1] // New order of 0-based indices from original sections array
}

7. Replace all sections (useful for "create/build page"):
{
  "action": "replace_all_sections",
  "sections": [
    {
      "name": "blockName",
      "enabled": true,
      "data": { ... }
    }
  ]
}

8. Navigate & highlight field in sidebar:
{
  "action": "navigate_to_field",
  "section_index": 0,
  "field_path": "headline"
}

9. Save and publish:
{
  "action": "save_page"
}

========================
SUMMARY
========================
- If user says "write content / change headline / polish / fix text": DO NOT ADD BLOCKS. Update existing fields in place.
- If user says "create page / build page": Generate a complete full page layout.
- Always output strict JSON.
PROMPT;
    }

    /**
     * Fetch active image assets from the database and storage.
     *
     * @param  array<int, array<string, mixed>>|null  $provided
     * @return array<int, array<string, mixed>>
     */
    public function getAvailableAssets(?array $provided = null): array
    {
        if (is_array($provided) && ! empty($provided)) {
            return $provided;
        }

        try {
            return Asset::where('is_directory', false)
                ->orderByDesc('id')
                ->limit(60)
                ->get()
                ->filter(function ($asset) {
                    return Storage::disk('public')->exists($asset->path);
                })
                ->map(function ($asset) {
                    $url = '/storage/'.$asset->path;

                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'url' => $url,
                        'path' => $asset->path,
                        'width' => $asset->width,
                        'height' => $asset->height,
                        'mime' => $asset->mime,
                        'size' => $asset->size,
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('AiAgentService asset query failed: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Search image assets by keyword / query for AI agent tool calls.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchAssets(string $query = '', int $limit = 30): array
    {
        try {
            $builder = Asset::where('is_directory', false)
                ->where(function ($q) {
                    $q->where('mime', 'like', 'image/%')
                        ->orWhere('path', 'like', '%.jpg')
                        ->orWhere('path', 'like', '%.jpeg')
                        ->orWhere('path', 'like', '%.png')
                        ->orWhere('path', 'like', '%.webp')
                        ->orWhere('path', 'like', '%.svg');
                });

            if (! empty(trim($query))) {
                $terms = array_filter(explode(' ', trim($query)));
                $builder->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->where(function ($sq) use ($term) {
                            $sq->where('name', 'like', "%{$term}%")
                                ->orWhere('path', 'like', "%{$term}%");
                        });
                    }
                });
            }

            return $builder->orderByDesc('id')
                ->limit($limit)
                ->get()
                ->filter(function ($asset) {
                    return Storage::disk('public')->exists($asset->path);
                })
                ->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'url' => '/storage/'.$asset->path,
                        'path' => $asset->path,
                        'width' => $asset->width,
                        'height' => $asset->height,
                        'mime' => $asset->mime,
                        'size' => $asset->size,
                    ];
                })
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('AiAgentService asset search failed: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Parse JSON from model response safely.
     *
     * @return array{thought?: string, message?: string, actions?: array<int, array<string, mixed>>, suggestions?: array<int, string>}
     */
    protected function parseJsonResponse(string $raw): array
    {
        $trimmed = trim($raw);

        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $trimmed, $matches)) {
            $trimmed = trim($matches[1]);
        }

        $decoded = json_decode($trimmed, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        $firstBrace = strpos($trimmed, '{');
        $lastBrace = strrpos($trimmed, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $extracted = substr($trimmed, $firstBrace, $lastBrace - $firstBrace + 1);
            $decoded = json_decode($extracted, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [
            'thought' => 'Processed text response',
            'message' => $raw,
            'actions' => [],
            'suggestions' => ['Try again with a specific command'],
        ];
    }

    public static function getActiveModelName(): string
    {
        $settings = Setting::first();

        return (string) ($settings?->ai_model ?: config('services.deepseek.model', 'DeepSeek V4'));
    }
}
