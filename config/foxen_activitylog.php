<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | This is the name of the table that will be created by the migration and
    | used by the Activity model. You may change this value to anything you
    | wish.
    |
    */
    "table_name" => "activity_log",

    /*
    |--------------------------------------------------------------------------
    | Default Log Name
    |--------------------------------------------------------------------------
    |
    | This is the default log name that will be used when no log name is
    | specified on the model.
    |
    */
    "default_log_name" => "default",

    /*
    |--------------------------------------------------------------------------
    | Redact Attributes
    |--------------------------------------------------------------------------
    |
    | This is an array of attribute names that will be redacted from the
    | activity log. You may add any attribute names to this array.
    |
    */
    "redact_attributes" => ["password"],

    /*
    |--------------------------------------------------------------------------
    | Prune Activity Log
    |--------------------------------------------------------------------------
    |
    | This is a boolean value that determines whether or not the activity log
    | will be pruned. If this is set to true, you must also schedule the
    | model:prune command in your application's `routes/console.php` file.
    |
    */
    "prune_activity_log" => false,

    /*
    |--------------------------------------------------------------------------
    | Prune Older Than Days
    |--------------------------------------------------------------------------
    |
    | This is the number of days that must pass before an activity log entry
    | is considered prunable.
    |
    */
    "prune_older_than_days" => 90,
];
