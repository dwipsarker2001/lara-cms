<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['url', 'ip', 'user_agent'])]
class PageView extends Model
{
    //
}
