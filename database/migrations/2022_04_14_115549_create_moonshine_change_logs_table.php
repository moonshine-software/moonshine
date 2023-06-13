<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('moonshine_change_logs', static function (Blueprint $table): void {
            $table->id();

            $table->foreignId('moonshine_user_id');

            $table->morphs('changelogable');

            $table->longText('states_before');
            $table->longText('states_after');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moonshine_change_logs');
    }
};
