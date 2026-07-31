<?php

namespace App\Blocks\custom;

use App\Blocks\Block;
use App\Blocks\Field;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;

class BlogList extends Block
{
    public string $name = 'blogList';

    public string $label = 'Blog List';

    public bool $background = false;

    public function fields(): array
    {
        return [
            Field::select('postCollection', 'Select Collection', self::collectionOptions(), default: ''),
            Field::select('layout', 'Posts Layout', [
                ['value' => 'grid', 'label' => 'Grid (Box)'],
                ['value' => 'list', 'label' => 'List'],
            ], default: 'grid'),
            Field::number('postsPerPage', 'Posts Per Page', default: 6),
        ];
    }

    /** Helper to build collection select options */
    protected static function collectionOptions(): array
    {
        $options = [
            ['value' => '', 'label' => 'All Post Collections (Auto)'],
        ];

        try {
            if (Schema::hasTable('collections')) {
                $cols = Collection::select('slug', 'name')->get();
                foreach ($cols as $col) {
                    if ($col->slug !== 'pages') {
                        $options[] = [
                            'value' => $col->slug,
                            'label' => $col->name,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return $options;
    }
}
