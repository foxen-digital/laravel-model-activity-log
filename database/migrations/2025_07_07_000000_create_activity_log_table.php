<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('foxen_activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->default(config('foxen_activitylog.default_log_name', 'default'));
            $table->string('event');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('causer_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->text('description');
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['causer_type', 'causer_id']);
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('foxen_activitylog.table_name', 'activity_log'));
    }
};
