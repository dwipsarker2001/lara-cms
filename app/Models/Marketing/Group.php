<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $table = 'groups';

    protected $fillable = [
        'user_id', 'name', 'description',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
