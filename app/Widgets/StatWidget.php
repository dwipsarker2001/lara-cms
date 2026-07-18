<?php

namespace App\Widgets;

class StatWidget extends Widget
{
    public static function type(): string
    {
        return 'stat';
    }

    public static function zone(): string
    {
        return 'grid';
    }

    public function label(): string
    {
        return 'Current Tickets';
    }

    public function render()
    {
        return view('admin.widgets.stat', ['widget' => (object) [
            'label' => $this->label(),
            'value' => '3,484',
            'delta' => '+7.1%',
            'up' => true,
            'data' => [12, 18, 10, 22, 16, 26, 20, 30, 24, 34],
        ]]);
    }
}
