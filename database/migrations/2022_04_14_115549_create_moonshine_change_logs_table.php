<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\MoonShineAuth;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moonshine_change_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('moonshine_user_id')
                ->constrained(
                    MoonShineAuth::model()?->getTable(),
                    MoonShineAuth::model()?->getKeyName()
                )
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->integer('changelogable_id');
            $table->string('changelogable_type');

            $table->longText('states_before');
            $table->longText('states_after');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moonshine_change_logs');
    }
};
