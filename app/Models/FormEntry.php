<?php

namespace App\Models;

use Database\Factories\FormEntryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEntry extends Model
{
    /** @use HasFactory<FormEntryFactory> */
    use HasFactory;

    protected $fillable = ['form_id', 'data', 'ip_address', 'user_agent', 'status'];

    protected function casts(): array
    {
        return [
            'form_id' => 'integer',
            'data' => 'array',
            'status' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (FormEntry $entry) {
            $form = $entry->form;
            $formTitle = $form ? $form->title : 'Form';
            $formIcon = $form && $form->icon ? $form->icon : 'comments';

            // Remove fa prefixes from icon name if present to keep it simple
            if (str_starts_with($formIcon, 'fa-solid ')) {
                $formIcon = substr($formIcon, 9);
            } elseif (str_starts_with($formIcon, 'fa-')) {
                $formIcon = substr($formIcon, 3);
            }

            $sub = '';
            if (is_array($entry->data)) {
                $parts = [];
                foreach (['full_name', 'name', 'email', 'phone'] as $key) {
                    if (! empty($entry->data[$key])) {
                        $parts[] = $entry->data[$key];
                    }
                }
                if (empty($parts)) {
                    foreach ($entry->data as $k => $v) {
                        if (! empty($v) && is_string($v)) {
                            $parts[] = "$k: $v";
                            if (count($parts) >= 2) {
                                break;
                            }
                        }
                    }
                }
                $sub = implode(' - ', $parts);
            }

            if (empty($sub)) {
                $sub = 'New submission received';
            }

            Notification::create([
                'title' => "New Entry: {$formTitle}",
                'sub' => $sub,
                'icon' => $formIcon,
                'tone' => 'text-text-muted',
            ]);
        });
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
