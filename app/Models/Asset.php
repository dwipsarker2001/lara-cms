<?php

namespace App\Models;

use App\Services\CloudflareR2Service;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_directory' => 'boolean',
        ];
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->is_directory || empty($this->path)) {
            return null;
        }

        if ($this->disk === 'r2') {
            $r2 = app(CloudflareR2Service::class);

            return $r2->getUrl($this->path);
        }

        return '/storage/'.ltrim($this->path, '/');
    }
}
