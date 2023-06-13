<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('moonshine_user_permissions', static function (Blueprint $table): void {
            $table->id();

            $table->foreignId('moonshine_user_id');

            $table->json('permissions');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moonshine_user_permissions');
    }
};
