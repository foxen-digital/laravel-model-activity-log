<?php

namespace Foxen\LaravelModelActivityLog\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use MassPrunable;

    protected $casts = [
        "properties" => "json",
    ];

    public function getTable()
    {
        return config("foxen_activitylog.table_name", "activity_log");
    }

    public function prunable(): Builder
    {
        if (!config("foxen_activitylog.prune_activity_log")) {
            return static::query()->whereRaw("1 = 0"); // Never prune if disabled
        }

        return static::query()->where(
            "created_at",
            "<= ",
            now()->subDays(
                config("foxen_activitylog.prune_older_than_days", 90)
            )
        );
    }

    public function scopeWhereSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where("subject_type", $subject->getMorphClass())
            ->where("subject_id", $subject->getKey());
    }

    public function scopeForSubjectType(
        Builder $query,
        string $subjectType
    ): Builder {
        return $query->where("subject_type", $subjectType);
    }

    public function scopeWhereCauser(Builder $query, Model $causer): Builder
    {
        return $query
            ->where("causer_type", $causer->getMorphClass())
            ->where("causer_id", $causer->getKey());
    }

    public function scopeForCauserType(
        Builder $query,
        string $causerType
    ): Builder {
        return $query->where("causer_type", $causerType);
    }

    public function scopeForEvent(Builder $query, string $event): Builder
    {
        return $query->where("event", $event);
    }
}
