<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_admin')->default(false);
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('status')->default('confirmed');
            $table->string('category')->default('Meeting');
            $table->string('organizer_name')->nullable();
            $table->string('organizer_email')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->string('visibility')->default('workspace');
            $table->unsignedInteger('reminder_minutes')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn([
                'start_time',
                'end_time',
                'status',
                'category',
                'organizer_name',
                'organizer_email',
                'capacity',
                'visibility',
                'reminder_minutes',
                'reminder_sent_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_admin');
        });
    }
};
