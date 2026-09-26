<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->decimal('advance_amount', 12, 2)->default(0);
            $table->decimal('repaid_amount', 12, 2)->default(0);
            $table->decimal('monthly_installment', 10, 2)->default(0);
            $table->date('advance_date');
            $table->string('payment_method')->default('cash');
            $table->string('status')->default('approved');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('staff_advance_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_advance_id')->nullable()->constrained('staff_advances')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->onDelete('set null');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('repayment_date');
            $table->string('repayment_type')->default('cash');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_advance_repayments');
        Schema::dropIfExists('staff_advances');
    }
};
