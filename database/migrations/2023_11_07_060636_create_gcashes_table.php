<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gcashes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->foreignId('user_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->string('ewallet') ->nullable();
            $table->string('name') ->nullable();
            $table->string('amount') ->nullable();
            $table->string('phone') ->nullable();
            $table->string('image') ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gcashes');
    }
};
