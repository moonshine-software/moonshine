<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', static function (Blueprint $table): void {
            $table->id();

            $table->string('name');

            $table->integer('start_point')->default(0);

            $table->integer('end_point')->default(100);

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

            $table->string('file')->nullable();

            $table->string('files')->nullable();

            $table->json('data')->nullable();

            $table->text('content')->nullable();

            $table->foreignIdFor(MoonshineUser::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamp('public_at')->nullable();

            $table->boolean('active')->default(false);

            $table->timestamps();
        });

        Schema::create('category_item', static function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Category::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignIdFor(Item::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('pivot_1')->nullable();
            $table->string('pivot_2')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_item');
        Schema::dropIfExists('items');
    }
};
