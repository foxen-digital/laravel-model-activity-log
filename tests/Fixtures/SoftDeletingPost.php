<?php

namespace Foxen\LaravelModelActivityLog\Tests\Fixtures;

use Foxen\LaravelModelActivityLog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletingPost extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];
}
