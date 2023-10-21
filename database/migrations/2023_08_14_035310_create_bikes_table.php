<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->string('image')
            ->nullable();
            $table->foreignId('category_id')
                ->contrained()
                ->nullable()
                ->onDelete('cascade');
            $table->string('name');
            $table->string('brand');
            $table->string('price');
            $table->string('rate');
            $table->text('description');
            $table->string('is_available')->default('available');
            $table->string('status')
            ->default('pending');
            $table->foreignId('user_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->foreignId('branch_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->string('color')->nullable();
            $table->string('down')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bikes');
    }
};
