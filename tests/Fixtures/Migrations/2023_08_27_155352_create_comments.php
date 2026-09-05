<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Tests\Fixtures\Enums\TestEnumColor;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('item_id');
            $table->string('color')->default(TestEnumColor::Black);
            $table->boolean('active')->default(1);
            $table->text('content');
            $table->json('data')->nullable();
            $table->nullableMorphs('imageable');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('moonshine_users')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
