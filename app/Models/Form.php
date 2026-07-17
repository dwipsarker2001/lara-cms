<?php

namespace App\Models;

use Database\Factories\FormFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    /** @use HasFactory<FormFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'submit_text', 'success_message', 'position', 'fields'];

    protected function casts(): array
    {
        return ['fields' => 'array'];
    }
}
