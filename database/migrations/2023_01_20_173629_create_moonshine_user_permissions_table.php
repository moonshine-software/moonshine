<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('moonshine_user_permissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('moonshine_user_id');

            $table->json('permissions');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moonshine_user_permissions');
    }
};
