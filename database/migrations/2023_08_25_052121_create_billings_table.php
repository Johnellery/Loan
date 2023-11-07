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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->foreignId('branch_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->foreignId('user_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->foreignId('applicant_user_id')
            ->contrained()
            ->nullable()
            ->onDelete('cascade');
            $table->string('transaction_number');
            $table->string('cashier')
            ->nullable();
            $table->string('payment_type')
            ->nullable();
            $table->string('payer_id')
            ->nullable();
            $table->string('amount');
            $table->string('amountpdf')
            ->nullable();
            $table->text('signature')
            ->nullable();
            $table->string('currency')
            ->nullable();
            $table->string('is_processed')
            ->default('false');
            $table->string('interests')
            ->default('0');
            $table->string('billing_status')
            ->default('processing');
            $table->string('image') ->nullable();
            $table->string('phone') ->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
