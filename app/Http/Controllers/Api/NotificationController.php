<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Support\NotificationCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Notification::query()->latest();
        if ($request->boolean('unread')) {
            $query->unread();
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $notifications = $query->paginate(min((int) $request->query('per_page', 15), 50));

        $data = collect($notifications->items())->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->title,
            'sub' => $n->sub,
            'icon' => $n->icon,
            'tone' => $n->tone,
            'type' => $n->type,
            'url' => $n->url,
            'is_read' => $n->isRead(),
            'time' => $n->formatted_time,
            'period' => $n->period,
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => Notification::unread()->count(),
            'data' => $data,
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sub' => 'nullable|string',
            'icon' => 'nullable|string',
            'type' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        $notification = NotificationCenter::make($validated['title'])
            ->sub($validated['sub'] ?? '')
            ->icon($validated['icon'] ?? 'bell')
            ->type($validated['type'] ?? 'info')
            ->url($validated['url'] ?? null)
            ->send();

        return response()->json(['success' => true, 'data' => $notification], 201);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->markAsRead();

        return response()->json(['success' => true, 'unread_count' => Notification::unread()->count()]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::unread()->update(['read_at' => now()]);

        return response()->json(['success' => true, 'unread_count' => 0]);
    }
}
