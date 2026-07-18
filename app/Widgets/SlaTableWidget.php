<?php

namespace App\Widgets;

class SlaTableWidget extends Widget
{
    public static function type(): string
    {
        return 'sla_table';
    }

    public static function zone(): string
    {
        return 'table';
    }

    public function label(): string
    {
        return 'SLA Monitoring';
    }

    public function render()
    {
        $rows = [
            (object) ['id' => '#2319', 'subject' => 'Payment failed on invoice', 'priority' => 'High', 'agent' => 'John Doe', 'status' => 'In Review', 'created' => '2025-08-18', 'due' => '2h left'],
            (object) ['id' => '#2320', 'subject' => 'Login issue', 'priority' => 'Medium', 'agent' => 'Sarah Lee', 'status' => 'Delivered', 'created' => '2025-08-19', 'due' => '1h left'],
            (object) ['id' => '#2321', 'subject' => 'Feature request export', 'priority' => 'Low', 'agent' => 'John Doe', 'status' => 'In Progress', 'created' => '2025-08-19', 'due' => '1d left'],
            (object) ['id' => '#2322', 'subject' => 'Contract renewal issue', 'priority' => 'Medium', 'agent' => 'Michael Wong', 'status' => 'In Progress', 'created' => '2025-08-20', 'due' => '9h left'],
        ];

        $agentPhotos = [
            'John Doe' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=80&h=80&fit=crop&crop=face',
            'Sarah Lee' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop&crop=face',
            'Michael Wong' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face',
        ];

        $avatarColors = ['bg-indigo-500', 'bg-rose-500', 'bg-amber-500', 'bg-sky-500'];

        return view('admin.widgets.sla-table', compact('rows', 'agentPhotos', 'avatarColors'));
    }
}
