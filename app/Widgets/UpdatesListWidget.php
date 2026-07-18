<?php

namespace App\Widgets;

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
        return 'Latest Updates';
    }

    public function render()
    {
        $updates = [
            (object) ['title' => 'New Client Added', 'sub' => 'PT. Alpha Indonesia registered', 'time' => '11:15 AM', 'icon' => 'user-plus', 'tone' => 'text-text-muted'],
            (object) ['title' => 'Agent Reassigned', 'sub' => 'Ticket #2322 moved to Michael Wong', 'time' => '11:00 AM', 'icon' => 'comments', 'tone' => 'text-text-muted'],
            (object) ['title' => 'SLA Breach Risk', 'sub' => "Ticket #2320 'Login issue'", 'time' => '10:45 AM', 'icon' => 'triangle-exclamation', 'tone' => 'text-red-500'],
            (object) ['title' => 'Knowledge Base', 'sub' => "New article published: 'Login Troubleshooting'", 'time' => '10:30 AM', 'icon' => 'book-open', 'tone' => 'text-text-muted'],
            (object) ['title' => 'Customer Feedback', 'sub' => "'Great support response, thanks Sarah!'", 'time' => '10:30 AM', 'icon' => 'star', 'tone' => 'text-amber-500'],
        ];

        return view('admin.widgets.updates-list', compact('updates'));
    }
}
