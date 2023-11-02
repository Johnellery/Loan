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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_clearance')->nullable();
            $table->string('valid_id')->nullable();
            $table->string('picture')->nullable();
            $table->string('valid_id_list')->nullable();
            $table->string('first');
            $table->string('middle')->nullable();
            $table->string('last');
            $table->string('age');
            $table->string('gender');
            $table->string('contact_applicant')->nullable();
            $table->string('civil');
            $table->string('religion');
            $table->string('unit');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->string('spouse')->nullable();
            $table->string('occupation');
            $table->string('contact_spouse')->nullable();
            $table->string('occupation_spouse')->nullable();
            $table->string('term');
            $table->string('installment');
            $table->string('down_payment')->nullable();
            $table->foreignId('bike_id')
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
            $table->string('loan_date')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('fullname')->nullable();
            $table->string('bike_price')->nullable();
            $table->string('total_interest')->nullable();
            $table->string('payment')->nullable();
            $table->string('plus')->nullable();
            $table->string('minus_principle')->nullable();
            $table->string('remaining_balance')->nullable();
            $table->string('remaining_weeks')->nullable();
            $table->string('status')->default('pending');
            $table->string('is_status')->default('active');
            $table->string('ci_status')->default('pending');
            $table->string('is_paid')->nullable();
            $table->string('payment_description')->nullable();
            $table->string('is_computed')->default('false');
            $table->json('repossession')->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->string('ci_sched')->nullable();
            $table->timestamp('payment_schedule')->nullable();
            $table->string('payment_schedule_slug')->nullable();
            $table->string('payment_date')->nullable();
            $table->timestamp('week11')->nullable();
            $table->timestamp('week12')->nullable();
            $table->timestamp('week13')->nullable();
            $table->timestamp('week14')->nullable();
            $table->timestamp('week21')->nullable();
            $table->timestamp('week22')->nullable();
            $table->timestamp('week23')->nullable();
            $table->timestamp('week24')->nullable();
            $table->timestamp('week31')->nullable();
            $table->timestamp('week32')->nullable();
            $table->timestamp('week33')->nullable();
            $table->timestamp('week34')->nullable();
            $table->timestamp('week41')->nullable();
            $table->timestamp('week42')->nullable();
            $table->timestamp('week43')->nullable();
            $table->timestamp('week44')->nullable();
            $table->timestamp('week51')->nullable();
            $table->timestamp('week52')->nullable();
            $table->timestamp('week53')->nullable();
            $table->timestamp('week54')->nullable();
            $table->timestamp('week61')->nullable();
            $table->timestamp('week62')->nullable();
            $table->timestamp('week63')->nullable();
            $table->timestamp('week64')->nullable();
            $table->timestamp('month1')->nullable();
            $table->timestamp('month2')->nullable();
            $table->timestamp('month3')->nullable();
            $table->timestamp('month4')->nullable();
            $table->timestamp('month5')->nullable();
            $table->timestamp('month6')->nullable();
            // $table->string('paid1')->nullable();
            // $table->string('paid2')->nullable();
            // $table->string('paid3')->nullable();
            // $table->string('paid4')->nullable();
            // $table->string('paid5')->nullable();
            // $table->string('paid6')->nullable();
            $table->string('progress')->default('0');
            $table->timestamp('status_date')->nullable();
            $table->timestamp('ci_date')->nullable();
            $table->string('remark')->nullable();
            $table->string('ci_remark')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
