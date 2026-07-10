<?php

namespace App\Blocks;

use Illuminate\Support\Str;

/**
 * Block
 * Base class for every content block. To create a new section a developer only:
 *
 *   1. Extends this class in app/Blocks (any subfolder), sets $name + $label,
 *      and returns the editable fields from fields().
 *   2. Creates the matching Blade view resources/views/blocks/{kebab-name}.blade.php
 *      which receives $data and renders the HTML.
 *
 * Everything else — registration, the editor form, default content, public
 * rendering — is automatic.
 */
abstract class Block
{
    /** Machine name, e.g. "heroBanner". Referenced by sections and the view. */
    public string $name;

    /** Human label shown in the block picker. */
    public string $label;

    /** Global blocks (navbar/footer) are shared site-wide, edited on the home page. */
    public bool $global = false;

    /**
     * Whether to auto-prepend a `background` field (image/color/opacity wrapper).
     * Full-bleed blocks with their own background (hero, navbar) set this false.
     */
    public bool $background = true;

    /**
     * The block's editable fields.
     *
     * @return array<int, array>
     */
    abstract public function fields(): array;

    /** Blade view name. Defaults to blocks.{kebab-name}; override if needed. */
    public function view(): string
    {
        return 'blocks.'.Str::kebab($this->name);
    }

    /**
     * Fields as the editor and renderer see them: a `background` field prepended
     * unless the block opts out or is global.
     *
     * @return array<int, array>
     */
    public function resolvedFields(): array
    {
        if ($this->background && ! $this->global) {
            return [Field::background(), ...$this->fields()];
        }

        return $this->fields();
    }

    /** Serializable shape handed to the admin editor (JSON). */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'global' => $this->global,
            'fields' => $this->resolvedFields(),
        ];
    }
}
