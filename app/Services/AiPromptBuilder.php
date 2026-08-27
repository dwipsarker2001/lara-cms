<?php

namespace App\Services;

use Illuminate\Support\Facades\View;

class AiPromptBuilder
{
    /**
     * Build the primary AI Copilot system prompt using the dedicated prompt template.
     *
     * @param  array<string, mixed>  $context
     */
    public static function build(array $context): string
    {
        $schemas = $context['schemas'] ?? null;
        $blockList = $context['blockList'] ?? null;
        $sections = $context['sections'] ?? [];
        $activeSectionIndex = $context['activeSectionIndex'] ?? null;
        $activeSectionName = $context['activeSectionName'] ?? null;
        $activeSectionData = $context['activeSectionData'] ?? null;
        $assets = $context['assets'] ?? null;
        $entryData = $context['entryData'] ?? [];

        // Compact JSON — token-efficient format
        $sectionsJson = json_encode($sections, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $entryJson = json_encode($entryData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $activeInfo = ($activeSectionIndex !== null && $activeSectionIndex !== '')
            ? "Active section in sidebar: index={$activeSectionIndex}, block={$activeSectionName}"
            : 'No section active (user on section list / page overview)';

        $activeDataBlock = '';
        if ($activeSectionData !== null) {
            $activeDataJson = json_encode($activeSectionData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $activeDataBlock = "\nActive section full data:\n{$activeDataJson}";
        }

        $schemasBlock = '';
        if ($schemas !== null) {
            $blockListJson = json_encode($blockList ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $schemasJson = json_encode($schemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $schemasBlock = <<<SCHEMA

========================
AVAILABLE BLOCKS & SCHEMAS (sent once per session on change)
========================
Block names: {$blockListJson}
Full schemas: {$schemasJson}
SCHEMA;
        }

        $assetsBlock = '';
        if ($assets !== null) {
            $assetsJson = json_encode($assets, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $assetsBlock = <<<ASSETS

========================
AVAILABLE MEDIA & STOCK PHOTOS (Local uploads + Outsourced Stock Photos from Pixabay/Pexels/Unsplash)
========================
{$assetsJson}
STRICT RULE FOR IMAGES:
1. ALWAYS pick and use image URLs from this list for any image field (`backgroundImage`, `image`, `heroImage`, `gallery.0.image`, etc.).
2. If local uploaded images match the topic, you may use them.
3. If local uploaded images do NOT match or are irrelevant to the topic, OUTSOURCE THE IMAGE by picking the matching stock photo URL (Pixabay/Pexels/Unsplash) from this list!
4. Match the image `name` (keywords/tags) to the section topic.
5. NEVER invent or hallucinate broken URLs outside this list.
ASSETS;
        }

        $collections = $context['collections'] ?? null;
        $collectionsBlock = '';
        if (! empty($collections)) {
            $colsJson = json_encode($collections, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $collectionsBlock = <<<COLS

========================
AVAILABLE COLLECTIONS & ENTRIES (use these to set links or select collection items)
========================
{$colsJson}
COLS;
        }

        $pageTitle = ! empty($entryData['title'])
            ? (string) $entryData['title']
            : (! empty($context['slug']) ? (string) $context['slug'] : 'Current Page');

        $viewData = [
            'pageTitle' => $pageTitle,
            'entryJson' => $entryJson,
            'activeInfo' => $activeInfo,
            'activeDataBlock' => $activeDataBlock,
            'sectionsJson' => $sectionsJson,
            'schemasBlock' => $schemasBlock,
            'collectionsBlock' => $collectionsBlock,
            'assetsBlock' => $assetsBlock,
        ];

        if (View::exists('admin.ai.system-prompt')) {
            return trim(View::make('admin.ai.system-prompt', $viewData)->render());
        }

        // Fallback if view template is missing
        return static::fallbackSystemPrompt($viewData);
    }

    /**
     * Build the agentic loop system prompt using the dedicated prompt template.
     */
    public static function buildAgent(): string
    {
        if (View::exists('admin.ai.agent-prompt')) {
            return trim(View::make('admin.ai.agent-prompt')->render());
        }

        return <<<'PROMPT'
You are an autonomous AI agent embedded in Lara-CMS, a block-based CMS for building websites.
You have tools to read page data on demand and apply changes section by section.
PROMPT;
    }

    /**
     * Fallback prompt string in case view caching or rendering is unavailable.
     *
     * @param  array<string, mixed>  $data
     */
    protected static function fallbackSystemPrompt(array $data): string
    {
        return <<<PROMPT
You are an autonomous AI Visual Copilot built into Lara-CMS.
The current page title is: "{$data['pageTitle']}"
{$data['activeInfo']}
{$data['activeDataBlock']}
Page sections:
{$data['sectionsJson']}
Entry metadata: {$data['entryJson']}
{$data['schemasBlock']}
{$data['collectionsBlock']}
{$data['assetsBlock']}
Always respond in valid JSON with an actions array.
PROMPT;
    }
}
