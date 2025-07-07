# Laravel Model Activity Log

A simple Laravel package to automatically record basic activity (creation, updates with attribute changes, deletion, restoration) performed on specified Eloquent models, and provide a mechanism for automatically pruning old log entries.

## Installation

You can install the package via composer:

```bash
composer require foxen/laravel-model-activity-log
```

The package will automatically register its service provider.

To publish the configuration file, run the following command:

```bash
php artisan vendor:publish --provider="Foxen\LaravelModelActivityLog\Providers\ActivityLogServiceProvider" --tag="config"
```

This will create a `foxen_activitylog.php` file in your `config` directory.

Finally, you need to run the migrations to create the `activity_log` table:

```bash
php artisan migrate
```

## Usage

To enable activity logging for a model, simply use the `LogsActivity` trait in your model class:

```php
use Illuminate\Database\Eloquent\Model;
use Foxen\LaravelModelActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;

    // ...
}
```

### Customizing the Log Name

By default, the log name is set to `default`. You can customize this on a per-model basis by adding a `protected $activityLogName` property to your model:

```php
class Post extends Model
{
    use LogsActivity;

    protected $activityLogName = 'posts';

    // ...
}
```

### Ignoring Attributes

To exclude certain attributes from the activity log when a model is created or updated, you can add a `protected $ignoreActivityLogAttributes` property to your model:

```php
class Post extends Model
{
    use LogsActivity;

    protected $ignoreActivityLogAttributes = ['updated_at'];

    // ...
}
```

### Redacting Attributes

To redact sensitive attributes from the activity log, you can add a `protected $redactedActivityLogAttributes` property to your model. The attribute key will be logged, but the value will be replaced with `[REDACTED]`.

```php
class User extends Model
{
    use LogsActivity;

    protected $redactedActivityLogAttributes = ['password', 'remember_token'];

    // ...
}
```

You can also configure global redacted attributes in the `config/foxen_activitylog.php` file.

### Retrieving Logs

You can retrieve activity logs using the `Foxen\LaravelModelActivityLog\Models\Activity` model. The package provides several convenient query scopes:

```php
use Foxen\LaravelModelActivityLog\Models\Activity;

// Get all activity for a specific model instance
$activities = Activity::whereSubject($post)->get();

// Get all activity for a specific model type
$activities = Activity::forSubjectType('App\Models\Post')->get();

// Get all activity caused by a specific user
$activities = Activity::whereCauser($user)->get();

// Get all activity for a specific causer type
$activities = Activity::forCauserType('App\Models\User')->get();

// Get all activity for a specific event
$activities = Activity::forEvent('created')->get();
```

### Pruning Logs

The package can automatically prune old activity log entries. To enable this, set the `prune_activity_log` option to `true` in your `config/foxen_activitylog.php` file and configure the `prune_older_than_days` option.

```php
// config/foxen_activitylog.php

return [
    // ...
    'prune_activity_log' => true,
    'prune_older_than_days' => 30,
];
```

Once enabled, you must schedule the `model:prune` command in your application's `app/Console/Kernel.php` file:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('model:prune')->daily();
}
```

## Testing

```bash
composer test
```
