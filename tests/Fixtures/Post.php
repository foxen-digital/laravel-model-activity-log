<?php

namespace Foxen\LaravelModelActivityLog\Tests\Fixtures;

use Foxen\LaravelModelActivityLog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use LogsActivity;

    protected $guarded = [];
}
