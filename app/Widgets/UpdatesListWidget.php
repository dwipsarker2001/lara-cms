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
