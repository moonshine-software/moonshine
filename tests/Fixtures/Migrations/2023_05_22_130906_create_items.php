<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Models\Category;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();

            $table->string('name');

            $table->text('content');

            $table->foreignIdFor(MoonshineUser::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignIdFor(Category::class)
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamp('public_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
