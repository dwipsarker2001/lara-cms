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

        // Build flatData mapping from replacements keys (without braces)
        $flatData = [];
        foreach ($replacements as $key => $val) {
            $cleanKey = trim(str_replace(['{{', '}}'], '', $key));
            $flatData[$cleanKey] = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
        }

        // 1. Process {% for ... %} ... {% endfor %} loops
        $html = preg_replace_callback('/\{%\s*for\s+(\w+)\s+in\s+(.+?)\s*%\}(.*?)\{%\s*endfor\s*%\}/s', function ($matches) use ($flatData) {
            $loopVar = $matches[1];
            $expr = trim($matches[2]);
            $body = $matches[3];

            $items = [];
            if (str_contains($expr, '.split(')) {
                preg_match('/(.+?)\.split\([\'"](.+?)[\'"]\)/', $expr, $splitMatches);
                if ($splitMatches) {
                    $path = trim($splitMatches[1]);
                    $delimiter = stripcslashes($splitMatches[2]);
                    $val = $flatData[$path] ?? '';
                    if ($val !== '') {
                        $items = explode($delimiter, $val);
                    }
                }
            } else {
                $indexes = [];
                foreach (array_keys($flatData) as $k) {
                    if (str_starts_with($k, $expr.'.')) {
                        $parts = explode('.', substr($k, strlen($expr) + 1));
                        if (is_numeric($parts[0])) {
                            $indexes[(int) $parts[0]] = true;
                        }
                    }
                }
                if (! empty($indexes)) {
                    ksort($indexes);
                    foreach (array_keys($indexes) as $idx) {
                        $items[] = $expr.'.'.$idx;
                    }
                }
            }

            $output = '';
            foreach ($items as $item) {
                $itemOutput = $body;
                if (is_string($item) && str_contains($item, '.')) {
                    $itemOutput = preg_replace_callback('/\{\{\s*'.$loopVar.'\.(.+?)\s*\}\}/', function ($subMatches) use ($flatData, $item) {
                        $subPath = trim($subMatches[1]);

                        return e($flatData[$item.'.'.$subPath] ?? '');
                    }, $itemOutput);
                } else {
                    $itemOutput = str_replace(['{{'.$loopVar.'}}', '{{ '.$loopVar.' }}'], e(trim($item)), $itemOutput);
                }
                $output .= $itemOutput;
            }

            return $output;
        }, $html);

        // 2. Process {% if ... %} ... {% endif %} conditionals
        $html = preg_replace_callback('/\{%\s*if\s+(.+?)\s*%\}(.*?)\{%\s*endif\s*%\}/s', function ($matches) use ($flatData) {
            $expr = trim($matches[1]);
            $body = $matches[2];

            $isTrue = false;
            if (preg_match('/(.+?)\s*(==|!=)\s*[\'"]?(.*?)[\'"]?$/', $expr, $opMatches)) {
                $path = trim($opMatches[1]);
                $op = $opMatches[2];
                $expected = trim($opMatches[3]);
                $val = trim($flatData[$path] ?? '');

                if ($op === '==') {
                    $isTrue = ($val === $expected);
                } elseif ($op === '!=') {
                    $isTrue = ($val !== $expected);
                }
            } else {
                $val = trim($flatData[$expr] ?? '');
                $isTrue = ($val !== '' && $val !== 'false' && $val !== '0');
            }

            return $isTrue ? $body : '';
        }, $html);

        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        if ($preview) {
            $html = '<div class="p-0.5">'.$html.'</div>';
        }

        return $html;
    }

    protected function collectPlaceholders(array $field, array $data, string $prefix, array &$replacements): void
    {
        $name = $field['name'];
        $value = $data[$name] ?? $field['defaultValue'] ?? null;

        $this->collectFieldData($field, $value, $prefix.$name, $replacements);
    }

    protected function collectFieldData(array $field, mixed $value, string $keyPath, array &$replacements): void
    {
        if ($field['type'] === 'object') {
            if (! empty($field['list'])) {
                $items = is_array($value) ? $value : [];
                for ($i = 0; $i < 20; $i++) {
                    $itemData = $items[$i] ?? [];
                    if (is_array($field['fields'] ?? null)) {
                        foreach ($field['fields'] as $subField) {
                            $subName = $subField['name'];
                            $subVal = $itemData[$subName] ?? $subField['defaultValue'] ?? null;
                            $this->collectFieldData($subField, $subVal, $keyPath.'.'.$i.'.'.$subName, $replacements);
                        }
                    }
                }
            } else {
                $objData = is_array($value) ? $value : [];
                if (is_array($field['fields'] ?? null)) {
                    foreach ($field['fields'] as $subField) {
                        $subName = $subField['name'];
                        $subVal = $objData[$subName] ?? $subField['defaultValue'] ?? null;
                        $this->collectFieldData($subField, $subVal, $keyPath.'.'.$subName, $replacements);
                    }
                }
            }
        } else {
            $stringValue = is_string($value) || is_numeric($value) || is_bool($value) ? (string) $value : '';
            foreach (['{{'.$keyPath.'}}', '{{ '.$keyPath.' }}'] as $key) {
                $replacements[$key] = e($stringValue);
            }
        }
    }
}
