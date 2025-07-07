<?php

namespace Foxen\LaravelModelActivityLog\Tests\Fixtures;

use Foxen\LaravelModelActivityLog\Traits\LogsActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use LogsActivity;

    protected $guarded = [];

    protected $redactedActivityLogAttributes = ["password"];
}
