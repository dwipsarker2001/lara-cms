<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['name', 'price', 'max_emails', 'max_contacts', 'max_campaigns', 'max_groups'])]
class SubscriptionPlan extends Model
{
    public function title(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }
}
