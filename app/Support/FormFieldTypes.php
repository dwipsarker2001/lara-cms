<?php

namespace App\Support;

use Illuminate\Support\Str;

class FormFieldTypes
{
    /**
     * @return list<array{name: string, label: string, previewHtml: string}>
     */
    public static function catalog(): array
    {
        return collect(static::definitions())
            ->map(fn (array $def): array => [
                'name' => $def['name'],
                'label' => $def['label'],
                'previewHtml' => $def['previewHtml'],
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{name: string, label: string, type: string, placeholder: string, required: bool, options?: list<string>}|null
     */
    public static function createDefaultField(string $type): ?array
    {
        $def = collect(static::definitions())->firstWhere('name', $type);

        if (! $def) {
            return null;
        }

        $field = [
            '_key' => (string) Str::uuid(),
            'type' => $def['name'],
            'label' => $def['label'],
            'name' => $def['name'].'_'.Str::lower(Str::random(4)),
            'placeholder' => $def['placeholder'] ?? '',
            'required' => false,
        ];

        if (($def['hasOptions'] ?? false) === true) {
            $field['options'] = ['Option 1', 'Option 2', 'Option 3'];
        }

        return $field;
    }

    /**
     * @return list<array{name: string, label: string, placeholder?: string, hasOptions?: bool, previewHtml: string}>
     */
    protected static function definitions(): array
    {
        $preview = static fn (string $inner): string => <<<HTML
<div style="padding:48px 64px;font-family:Inter,system-ui,sans-serif;background:#f8fafc;min-height:220px;box-sizing:border-box;">
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:28px 32px;box-shadow:0 1px 2px rgba(16,24,40,.04);">
        {$inner}
    </div>
</div>
HTML;

        return [
            [
                'name' => 'text',
                'label' => 'Text',
                'placeholder' => 'Enter text…',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Full name</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">Enter text…</div>
HTML),
            ],
            [
                'name' => 'email',
                'label' => 'Email',
                'placeholder' => 'you@example.com',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Email address</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">you@example.com</div>
HTML),
            ],
            [
                'name' => 'phone',
                'label' => 'Phone',
                'placeholder' => '+1 (555) 000-0000',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Phone number</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">+1 (555) 000-0000</div>
HTML),
            ],
            [
                'name' => 'number',
                'label' => 'Number',
                'placeholder' => '0',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Quantity</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">0</div>
HTML),
            ],
            [
                'name' => 'textarea',
                'label' => 'Textarea',
                'placeholder' => 'Write something…',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Message</label>
<div style="height:88px;border:1px solid #d1d5db;border-radius:8px;background:#fff;padding:12px 14px;color:#9ca3af;font-size:14px;">Write something…</div>
HTML),
            ],
            [
                'name' => 'select',
                'label' => 'Select',
                'hasOptions' => true,
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Choose option</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 14px;color:#6b7280;font-size:14px;">
    <span>Select…</span>
    <span style="color:#9ca3af;">▾</span>
</div>
HTML),
            ],
            [
                'name' => 'checkbox',
                'label' => 'Checkbox',
                'hasOptions' => true,
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:12px;">Preferences</label>
<div style="display:flex;flex-direction:column;gap:10px;">
    <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#374151;"><span style="width:18px;height:18px;border:1.5px solid #d1d5db;border-radius:4px;background:#fff;display:inline-block;"></span> Option 1</div>
    <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#374151;"><span style="width:18px;height:18px;border:1.5px solid #d1d5db;border-radius:4px;background:#fff;display:inline-block;"></span> Option 2</div>
</div>
HTML),
            ],
            [
                'name' => 'radio',
                'label' => 'Radio',
                'hasOptions' => true,
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:12px;">Choose one</label>
<div style="display:flex;flex-direction:column;gap:10px;">
    <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#374151;"><span style="width:18px;height:18px;border:1.5px solid #d1d5db;border-radius:999px;background:#fff;display:inline-block;"></span> Option 1</div>
    <div style="display:flex;align-items:center;gap:10px;font-size:14px;color:#374151;"><span style="width:18px;height:18px;border:1.5px solid #d1d5db;border-radius:999px;background:#fff;display:inline-block;"></span> Option 2</div>
</div>
HTML),
            ],
            [
                'name' => 'date',
                'label' => 'Date',
                'placeholder' => 'YYYY-MM-DD',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Date</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">YYYY-MM-DD</div>
HTML),
            ],
            [
                'name' => 'time',
                'label' => 'Time',
                'placeholder' => 'HH:MM',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Time</label>
<div style="height:44px;border:1px solid #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;padding:0 14px;color:#9ca3af;font-size:14px;">HH:MM</div>
HTML),
            ],
            [
                'name' => 'file',
                'label' => 'File upload',
                'previewHtml' => $preview(<<<'HTML'
<label style="display:block;font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;">Attachment</label>
<div style="height:96px;border:2px dashed #d1d5db;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:13px;font-weight:500;">Click or drag to upload</div>
HTML),
            ],
        ];
    }
}
