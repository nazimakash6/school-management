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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable()->unique();
            $table->string('item_name');
            $table->text('category')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('min_quantity_alert')->default(5);
            $table->string('unit')->default('Pcs');
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('total_cost', 10, 2)->default(0.00);
            $table->string('location')->nullable();
            $table->string('item_condition')->default('Good');
            $table->string('status')->default('Available');
            $table->date('purchase_date')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('payment_status')->default('Paid');
            $table->string('payment_method')->default('Cash');
            $table->string('image_proof')->nullable();
            $table->string('image_proof_type')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
