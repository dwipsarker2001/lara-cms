<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'sub', 'icon', 'tone', 'created_at'];

    public function getPeriodAttribute(): string
    {
        $createdAt = $this->created_at;
        if (! $createdAt) {
            return 'Today';
        }

        if ($createdAt->isToday()) {
            return 'Today';
        }

        if ($createdAt->isYesterday()) {
            return 'Yesterday';
        }

        return 'This week';
    }

    public function getFormattedTimeAttribute(): string
    {
        $createdAt = $this->created_at;
        if (! $createdAt) {
            return 'Just now';
        }

        if ($createdAt->isToday()) {
            return $createdAt->format('g:i A');
        }

        if ($createdAt->isYesterday()) {
            return 'Yesterday '.$createdAt->format('g:i A');
        }

        return $createdAt->format('M d');
    }
}
