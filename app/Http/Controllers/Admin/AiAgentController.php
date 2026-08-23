<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    public function __construct(
        protected AiAgentService $aiAgentService
    ) {}

    /**
     * Handle AI chat & action execution query.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'nullable|string',
            'messages' => 'nullable|array',
            'context' => 'nullable|array',
            'sections' => 'nullable|array',
            'schemas' => 'nullable|array',
            'blockList' => 'nullable|array',
            'entryData' => 'nullable|array',
        ]);

        $messages = $request->input('messages', []);

        if (empty($messages) && $request->filled('prompt')) {
            $messages = [
                ['role' => 'user', 'content' => (string) $request->input('prompt')],
            ];
        }

        $context = $request->input('context', []);

        if ($request->has('sections')) {
            $context['sections'] = $request->input('sections');
        }
        if ($request->has('schemas')) {
            $context['schemas'] = $request->input('schemas');
        }
        if ($request->has('blockList')) {
            $context['blockList'] = $request->input('blockList');
        }
        if ($request->has('entryData')) {
            $context['entryData'] = $request->input('entryData');
        }
        if ($request->has('activeSectionIndex')) {
            $context['activeSectionIndex'] = $request->input('activeSectionIndex');
        }
        if ($request->has('activeSectionName')) {
            $context['activeSectionName'] = $request->input('activeSectionName');
        }
        if ($request->has('activeSectionData')) {
            $context['activeSectionData'] = $request->input('activeSectionData');
        }
        if ($request->has('assets')) {
            $context['assets'] = $request->input('assets');
        }

        $result = $this->aiAgentService->chat($messages, $context);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Return available media assets for the AI agent.
     */
    public function assets(Request $request): JsonResponse
    {
        $assets = $this->aiAgentService->getAvailableAssets();

        return response()->json([
            'assets' => $assets,
            'count' => count($assets),
        ]);
    }

    /**
     * Search image assets by query for AI agent or editor.
     */
    public function searchImages(Request $request): JsonResponse
    {
        $query = (string) $request->input('q', $request->input('query', ''));
        $limit = min(50, max(1, (int) $request->input('limit', 20)));

        $images = $this->aiAgentService->searchAssets($query, $limit);

        return response()->json([
            'success' => true,
            'query' => $query,
            'count' => count($images),
            'images' => $images,
        ]);
    }

    /**
     * Alias for images list / search.
     */
    public function images(Request $request): JsonResponse
    {
        return $this->searchImages($request);
    }
}
