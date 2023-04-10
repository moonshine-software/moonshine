<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Models\MoonshineUser;

return new class extends Migration
{
    public function up()
    {
        Schema::create('moonshine_user_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(MoonshineUser::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->json('permissions');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moonshine_user_permissions');
    }
};
