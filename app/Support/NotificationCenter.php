<?php

namespace App\Support;

use App\Models\Notification;

class NotificationCenter
{
    protected string $title;

    protected string $sub = '';

    protected string $icon = 'bell';

    protected string $type = 'info';

    protected ?string $url = null;

    protected ?string $tone = null;

    protected ?string $actionLabel = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public static function make(string $title): static
    {
        return new static($title);
    }

    public function sub(string $sub): static
    {
        $this->sub = $sub;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function url(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function tone(?string $tone): static
    {
        $this->tone = $tone;

        return $this;
    }

    public function actionLabel(?string $actionLabel): static
    {
        $this->actionLabel = $actionLabel;

        return $this;
    }

    public function send(): Notification
    {
        $defaults = static::resolveTypeDefaults($this->type);

        return Notification::create([
            'title' => $this->title,
            'sub' => $this->sub,
            'icon' => $this->icon !== 'bell' ? $this->icon : ($defaults['icon'] ?? 'bell'),
            'tone' => $this->tone ?? $defaults['tone'] ?? 'text-text-muted',
            'type' => $this->type,
            'url' => $this->url,
            'action_label' => $this->actionLabel,
        ]);
    }

    public static function success(string $title, string $sub = '', ?string $url = null): Notification
    {
        return static::make($title)->sub($sub)->type('success')->url($url)->send();
    }

    public static function info(string $title, string $sub = '', ?string $url = null): Notification
    {
        return static::make($title)->sub($sub)->type('info')->url($url)->send();
    }

    public static function warning(string $title, string $sub = '', ?string $url = null): Notification
    {
        return static::make($title)->sub($sub)->type('warning')->url($url)->send();
    }

    public static function error(string $title, string $sub = '', ?string $url = null): Notification
    {
        return static::make($title)->sub($sub)->type('error')->url($url)->send();
    }

    public static function resolveTypeDefaults(string $type): array
    {
        return match ($type) {
            'success' => ['icon' => 'check-circle', 'tone' => 'text-emerald-600'],
            'warning' => ['icon' => 'triangle-exclamation', 'tone' => 'text-amber-600'],
            'error', 'danger' => ['icon' => 'x-circle', 'tone' => 'text-rose-600'],
            'primary' => ['icon' => 'star', 'tone' => 'text-indigo-600'],
            'purple' => ['icon' => 'sparkles', 'tone' => 'text-purple-600'],
            'cyan' => ['icon' => 'info', 'tone' => 'text-cyan-600'],
            default => ['icon' => 'bell', 'tone' => 'text-blue-600'],
        };
    }
}
