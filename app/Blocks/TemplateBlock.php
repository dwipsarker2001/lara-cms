<?php

namespace App\Blocks;

use App\Models\DynamicBlock;

class TemplateBlock extends Block
{
    protected DynamicBlock $dbBlock;

    protected array $fieldDefs;

    public string $template;

    public function __construct(DynamicBlock $dbBlock)
    {
        $this->dbBlock = $dbBlock;
        $this->name = $dbBlock->name;
        $this->label = $dbBlock->label;
        $this->global = $dbBlock->global;
        $this->background = $dbBlock->background;
        $this->fieldDefs = $dbBlock->fields;
        $this->template = $dbBlock->template;
    }

    public function fields(): array
    {
        return $this->fieldDefs;
    }

    public function render(array $data, string $_key = '', bool $preview = false, $page = null): string
    {
        $html = $this->template;

        $replacements = [];
        foreach ($this->resolvedFields() as $field) {
            $this->collectPlaceholders($field, $data, '', $replacements);
        }

        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        if ($preview) {
            $html = '<div class="p-0.5">'.$html.'</div>';
        }

        return $html;
    }

    protected function collectPlaceholders(array $field, array $data, string $prefix, array &$replacements): void
    {
        $name = $field['name'];
        $value = $data[$name] ?? $field['defaultValue'] ?? '';

        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                foreach (['{{'.$prefix.$name.'.'.$subKey.'}}', '{{ '.$prefix.$name.'.'.$subKey.' }}'] as $key) {
                    $replacements[$key] = e(is_string($subValue) ? $subValue : '');
                }
            }
        }

        foreach (['{{'.$prefix.$name.'}}', '{{ '.$prefix.$name.' }}'] as $key) {
            $replacements[$key] = e(is_string($value) ? $value : '');
        }
    }
}
