<?php

namespace Foxen\LaravelModelActivityLog\Traits;

use Foxen\LaravelModelActivityLog\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            static::logActivity($model, 'created');
        });

        static::updated(function (Model $model) {
            static::logActivity($model, 'updated');
        });

        static::deleted(function (Model $model) {
            static::logActivity($model, 'deleted');
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function (Model $model) {
                static::logActivity($model, 'restored');
            });
        }
    }

    protected static function logActivity(Model $model, string $event): void
    {
        $activity = new Activity;
        $activity->log_name =
            $model->activityLogName ??
            config('foxen_activitylog.default_log_name', 'default');
        $activity->event = $event;
        $activity->subject_type = $model->getMorphClass();
        $activity->subject_id = $model->getKey();
        $activity->causer_type = Auth::check()
            ? Auth::user()->getMorphClass()
            : null;
        $activity->causer_id = Auth::check() ? Auth::id() : null;
        $activity->description = static::getDescriptionForEvent($model, $event);
        $activity->properties = static::getPropertiesForEvent($model, $event);
        $activity->save();
    }

    protected static function getDescriptionForEvent(
        Model $model,
        string $event
    ): string {
        $modelName = class_basename($model);
        $modelId = $model->getKey();
        $actor = Auth::check() ? 'User' : 'System';

        return match ($event) {
            'created' => "{$actor} created {$modelName} ID: {$modelId}",
            'updated' => "{$actor} updated {$modelName} ID: {$modelId}",
            'deleted' => "{$actor} deleted {$modelName} ID: {$modelId}",
            'restored' => "{$actor} restored {$modelName} ID: {$modelId}",
            default => "{$actor} performed {$event} on {$modelName} ID: {$modelId}",
        };
    }

    protected static function getPropertiesForEvent(
        Model $model,
        string $event
    ): ?array {
        if ($event !== 'updated') {
            return null;
        }

        $dirty = $model->getDirty();
        $original = $model->getOriginal();

        $old = [];
        $new = [];

        $ignoreAttributes = array_merge(
            $model->ignoreActivityLogAttributes ?? [],
            ['updated_at']
        );

        $redactedAttributes = array_merge(
            $model->redactedActivityLogAttributes ?? [],
            config('foxen_activitylog.redact_attributes', [])
        );

        foreach ($dirty as $attribute => $value) {
            if (in_array($attribute, $ignoreAttributes)) {
                continue;
            }

            if (in_array($attribute, $redactedAttributes)) {
                $old[$attribute] = '[REDACTED]';
                $new[$attribute] = '[REDACTED]';

                continue;
            }

            $old[$attribute] = $original[$attribute] ?? null;
            $new[$attribute] = $value;
        }

        if (empty($old) && empty($new)) {
            return null;
        }

        return [
            'old' => $old,
            'new' => $new,
        ];
    }
}
