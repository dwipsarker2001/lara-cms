<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'sub', 'icon', 'tone', 'type', 'url', 'action_label', 'read_at', 'created_at',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): bool
    {
        return $this->isRead() ? true : $this->update(['read_at' => now()]);
    }

    public function getPeriodAttribute(): string
    {
        if (! $this->created_at) {
            return 'Today';
        }

        if ($this->created_at->isToday()) {
            return 'Today';
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday';
        }

        return 'This week';
    }

    public function getFormattedTimeAttribute(): string
    {
        if (! $this->created_at) {
            return 'Just now';
        }

        if ($this->created_at->isToday()) {
            return $this->created_at->format('g:i A');
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday '.$this->created_at->format('g:i A');
        }

        return $this->created_at->format('M d');
    }
}
