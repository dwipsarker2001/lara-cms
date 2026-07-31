<?php

namespace App\Widgets;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Support\Facades\DB;

class FormStatWidget extends Widget
{
    public function __construct(
        public ?int $formId = null,
    ) {}

    public static function type(): string
    {
        return 'form_stat';
    }

    public static function zone(): string
    {
        return 'grid';
    }

    public static function make(array $config): static
    {
        return new static(
            formId: isset($config['form_id']) ? (int) $config['form_id'] : null,
        );
    }

    public function label(): string
    {
        if ($this->formId) {
            $form = Form::find($this->formId);
            if ($form) {
                return 'Total '.$form->title;
            }
        }

        $firstForm = Form::orderBy('title')->first();
        if ($firstForm) {
            return 'Total '.$firstForm->title;
        }

        return 'Total Submissions';
    }

    public function render()
    {
        $forms = Form::orderBy('title')->get(['id', 'title']);

        if ($this->formId === null && $forms->isNotEmpty()) {
            $this->formId = $forms->first()->id;
        }

        $query = FormEntry::query();

        if ($this->formId) {
            $query->where('form_id', $this->formId);
        }

        $totalCount = (clone $query)->count();

        // 7-day sparkline data
        $daily = (clone $query)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $data = collect(range(6, 0))->map(fn ($d) => (int) $daily->get(now()->subDays($d)->format('Y-m-d'), 0))->toArray();

        // Calculate delta vs last week
        $thisWeekCount = (clone $query)->where('created_at', '>=', now()->subDays(6)->startOfDay())->count();
        $lastWeekCount = (clone $query)
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay()])
            ->count();

        if ($lastWeekCount > 0) {
            $diff = $thisWeekCount - $lastWeekCount;
            $percent = round(($diff / $lastWeekCount) * 100);
            $delta = ($percent >= 0 ? '+' : '').$percent.'%';
            $up = $percent >= 0;
        } else {
            $delta = '+'.$thisWeekCount;
            $up = true;
        }

        $selectedForm = $this->formId ? $forms->firstWhere('id', $this->formId) : null;
        $label = $selectedForm ? 'Total '.$selectedForm->title : 'Total Submissions';

        return view('admin.widgets.form-stat', [
            'forms' => $forms,
            'selectedFormId' => $this->formId,
            'selectedFormTitle' => $selectedForm?->title ?? 'All Forms',
            'widget' => (object) [
                'label' => $label,
                'value' => number_format($totalCount),
                'delta' => $delta,
                'up' => $up,
                'data' => $data,
            ],
        ]);
    }
}
