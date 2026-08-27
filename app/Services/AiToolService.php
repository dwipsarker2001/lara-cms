<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AiToolService
{
    /**
     * All tool definitions sent to the AI so it knows what it can call.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getToolDefinitions(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_sections',
                    'description' => 'Get a compact list of all sections on the current page. Returns index, block name, and enabled status only. Call this first to understand the page structure before editing.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_section',
                    'description' => 'Get the complete data of one specific section by its index number. Use this to read the exact current content of a section before editing it.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'index' => [
                                'type' => 'integer',
                                'description' => 'The section index (0-based) from get_sections()',
                            ],
                        ],
                        'required' => ['index'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_section_by_name',
                    'description' => 'Find a section by its block name (e.g. "faq", "hero", "testimonials", "gallery"). Returns the section index and full data.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => [
                                'type' => 'string',
                                'description' => 'Block name keyword to search for (case-insensitive partial match)',
                            ],
                        ],
                        'required' => ['name'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_schema',
                    'description' => 'Get the field schema for a specific block type. Shows all editable field names and their types. Call this before editing a section so you know the exact field names to use.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'block_name' => [
                                'type' => 'string',
                                'description' => 'The exact block name (e.g. "HeroBlock", "FaqBlock")',
                            ],
                        ],
                        'required' => ['block_name'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_block_list',
                    'description' => 'Get all available block names that can be added to the page. Use this before calling add_section.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_images',
                    'description' => 'Search the media library for images matching a keyword. Always call this before setting any image field — never guess or fabricate image URLs.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Search keyword (e.g. "beach", "team photo", "hotel lobby")',
                            ],
                            'limit' => [
                                'type' => 'integer',
                                'description' => 'Max results to return (default: 8, max: 20)',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'get_page_meta',
                    'description' => 'Get page metadata: entry data (title, slug, custom fields) and collection field definitions.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => (object) [],
                        'required' => [],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'apply_actions',
                    'description' => 'Apply a list of editor actions to the page. This is your FINAL call — it executes all changes and ends the agent loop. Do not call other tools after this.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'actions' => [
                                'type' => 'array',
                                'description' => 'Array of editor action objects (update_field, update_section, set_image, add_section, remove_section, save_page, etc.)',
                                'items' => ['type' => 'object'],
                            ],
                            'message' => [
                                'type' => 'string',
                                'description' => 'A concise markdown summary of what was changed, shown to the user.',
                            ],
                        ],
                        'required' => ['actions', 'message'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Dispatch a tool call by name and return its result.
     *
     * @param  array<string, mixed>  $context
     */
    public function dispatch(string $toolName, array $args, array $context): mixed
    {
        try {
            return match ($toolName) {
                'get_sections' => $this->getSections($context['full_sections'] ?? []),
                'get_section' => $this->getSection($context['full_sections'] ?? [], (int) ($args['index'] ?? 0)),
                'get_section_by_name' => $this->getSectionByName($context['full_sections'] ?? [], (string) ($args['name'] ?? '')),
                'get_schema' => $this->getSchema($context['schemas'] ?? [], (string) ($args['block_name'] ?? '')),
                'get_block_list' => $this->getBlockList($context['blockList'] ?? []),
                'search_images' => $this->searchImages((string) ($args['query'] ?? ''), min(20, (int) ($args['limit'] ?? 8))),
                'get_page_meta' => $this->getPageMeta($context['entryData'] ?? [], $context['collectionFields'] ?? []),
                default => ['error' => 'Unknown tool: '.$toolName],
            };
        } catch (\Throwable $e) {
            Log::warning("AiToolService dispatch error [{$toolName}]: ".$e->getMessage());

            return ['error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    //  Individual tool implementations
    // ─────────────────────────────────────────────

    /**
     * Return compact section list: index, name, enabled only.
     *
     * @param  array<int, array<string, mixed>>  $fullSections
     * @return array<string, mixed>
     */
    protected function getSections(array $fullSections): array
    {
        $compact = array_map(fn ($s, $i) => [
            'index' => $i,
            'name' => $s['name'] ?? 'unknown',
            'enabled' => $s['enabled'] ?? true,
        ], $fullSections, array_keys($fullSections));

        return [
            'total' => count($compact),
            'sections' => array_values($compact),
        ];
    }

    /**
     * Return full data for one section by index.
     *
     * @param  array<int, array<string, mixed>>  $fullSections
     * @return array<string, mixed>
     */
    protected function getSection(array $fullSections, int $index): array
    {
        if (! isset($fullSections[$index])) {
            return ['error' => "Section index {$index} not found. Total sections: ".count($fullSections)];
        }

        return [
            'index' => $index,
            'section' => $fullSections[$index],
        ];
    }

    /**
     * Find section by partial block name match.
     *
     * @param  array<int, array<string, mixed>>  $fullSections
     * @return array<string, mixed>
     */
    protected function getSectionByName(array $fullSections, string $name): array
    {
        $matches = [];
        foreach ($fullSections as $i => $section) {
            if (str_contains(strtolower($section['name'] ?? ''), strtolower($name))) {
                $matches[] = [
                    'index' => $i,
                    'section' => $section,
                ];
            }
        }

        if (empty($matches)) {
            return ['error' => "No section found matching '{$name}'. Available: ".implode(', ', array_column($fullSections, 'name'))];
        }

        return ['matches' => $matches];
    }

    /**
     * Return field schema for a block type.
     *
     * @param  array<string, mixed>  $schemas
     * @return array<string, mixed>
     */
    protected function getSchema(array $schemas, string $blockName): array
    {
        if (isset($schemas[$blockName])) {
            return [
                'block' => $blockName,
                'schema' => $schemas[$blockName],
            ];
        }

        // Case-insensitive fallback
        foreach ($schemas as $key => $schema) {
            if (strtolower($key) === strtolower($blockName)) {
                return ['block' => $key, 'schema' => $schema];
            }
        }

        return [
            'error' => "Schema not found for '{$blockName}'.",
            'available' => array_keys($schemas),
        ];
    }

    /**
     * Return all available block names.
     *
     * @param  array<int, string>  $blockList
     * @return array<string, mixed>
     */
    protected function getBlockList(array $blockList): array
    {
        return ['blocks' => $blockList, 'total' => count($blockList)];
    }

    /**
     * Search media library by keyword.
     *
     * @return array<string, mixed>
     */
    protected function searchImages(string $query, int $limit): array
    {
        $results = app(AiAgentService::class)->searchAssets($query, $limit);

        return [
            'query' => $query,
            'count' => count($results),
            'images' => $results,
        ];
    }

    /**
     * Return page metadata.
     *
     * @param  array<string, mixed>  $entryData
     * @param  array<string, mixed>  $collectionFields
     * @return array<string, mixed>
     */
    protected function getPageMeta(array $entryData, array $collectionFields): array
    {
        return [
            'entry' => $entryData,
            'collection_fields' => $collectionFields,
        ];
    }
}
